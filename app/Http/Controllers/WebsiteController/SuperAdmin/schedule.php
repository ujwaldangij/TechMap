<?php

namespace App\Http\Controllers\WebsiteController\SuperAdmin;

use Log;
use Illuminate\Http\Request;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\WebsiteModels\SuperAdmin\menu;
use App\Models\WebsiteModels\SuperAdmin\track;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Models\WebsiteModels\SuperAdmin\Credential;
use App\Models\WebsiteModels\SuperAdmin\SystemPage;
use App\Models\WebsiteModels\SuperAdmin\ComponyDetail;
use App\Models\WebsiteModels\SuperAdmin\schedule as sud;
use App\Models\WebsiteModels\SuperAdmin\doctor as SuperAdminDoctor;
use App\Models\WebsiteModels\SuperAdmin\schedule as SuperAdminSchedule;
use App\Models\WebsiteModels\SuperAdmin\Notification as SuperAdminNotification;
use App\Models\WebsiteModels\SuperAdmin\state;

class schedule extends Controller
{
    //
    public function schedule()
    {
        $title = SystemPage::where('name', 'schedule')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        // dd(session('user')->role);
        if (session('user')->role == 1) {
            $k = DB::select('SELECT s.id as s_id,s.name as s_name, c.username as created_user FROM state as s JOIN credentials as c
            ON s.created_id=c.id');
        } else {
            $k = DB::select('SELECT s.id as s_id,s.name as s_name, c.username as created_user FROM state as s JOIN credentials as c
            ON s.created_id=c.id WHERE c.id = ' . session('user')->id);
        }
        return view(
            'WebsitePages.SuperAdmin.schedule',
            [
                "title" => (!empty($title->title)) ? $title->title : 'schedule',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'schedule' => $k,
            ]
        );
    }
    public function add_person($id)
    {
        $title = SystemPage::where('name', 'add_person')->first();
        $doctor_data = SuperAdminSchedule::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.add_person',
            [
                "title" => (!empty($title->title)) ? $title->title : 'add_person',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data,
            ]
        );
    }
    public function add_person_post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "add_person" => ["required"],
            "agent_contact" => ["required"],
            "agent_schedule_datetime" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            DB::beginTransaction();
            $data = SuperAdminSchedule::where('id', $request['schedule_id'])->first();
            SuperAdminSchedule::where('id', $request['schedule_id'])->update([
                'agent' => $request['add_person'],
                'agent_contact' => $request['agent_contact'],
                'agent_schedule_datetime' => $request['agent_schedule_datetime'],
                'status' => 'agent align',
            ]);
            track::create([
                'status' => 'agent align',
                'doctor_id' => $data->doctor_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return redirect()->route('schedule');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating add_person_page: ' . $e->getMessage());
            return response()->json(['error' => 'Update failed'], 500);
        }
    }
    public function upload_report($id)
    {
        $title = SystemPage::where('name', 'upload_report')->first();
        $doctor_data = SuperAdminSchedule::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.upload_report',
            [
                "title" => (!empty($title->title)) ? $title->title : 'upload_report',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data,
            ]
        );
    }
    public function upload_report_post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "schedule_id" => ["required"],
            "result" => ["required"],
            "report" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $file = $request->file('report');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            // $file->storeAs('public/reports', $fileName);
            // $base64File = base64_encode(file_get_contents($file->path()));
            // dd($base64File);
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $publicPath = public_path('reports');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            $file->move($publicPath, $fileName);
            DB::beginTransaction();
            $data = SuperAdminSchedule::where('id', $request['schedule_id'])->first();
            SuperAdminSchedule::where('id', $request['schedule_id'])->update([
                'result' => $request['result'],
                'upload_report' => $fileName,
                'status' => 'report upload',
            ]);
            track::create([
                'status' => 'report upload',
                'doctor_id' => $data->doctor_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return redirect()->route('schedule');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating add_person_page: ' . $e->getMessage());
            return response()->json(['error' => 'Update failed'], 500);
        }
    }
    public function schedule_id_delete_post($id)
    {
        try {
            DB::beginTransaction();
            $k = SuperAdminSchedule::where('id', $id)->first();
            if (!empty($k->upload_report)) {
                $filePath = public_path('reports\\' . $k->upload_report);
                if (file_exists($filePath)) {
                    try {
                        unlink($filePath);
                    } catch (\Exception $e) {
                        error_log('Error deleting file: ' . $e->getMessage());
                    }
                }
            }
            SuperAdminSchedule::where('id', $id)->delete();
            DB::commit();
            return redirect()->route('schedule');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error delete SuperAdminDoctor: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function schedule_id_edit($id)
    {
        $title = SystemPage::where('name', 'schedule_id_edit')->first();
        $doctor_data = state::where('id', $id)->first();
        $doctor_data = state::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (session('user')->role == 1) {
            $k = DB::select('SELECT s.id as s_id,s.name as s_name, c.username as created_user FROM state as s JOIN credentials as c
            ON s.created_id=c.id');
        } else {
            $k = DB::select('SELECT s.id as s_id,s.name as s_name, c.username as created_user FROM state as s JOIN credentials as c
            ON s.created_id=c.id WHERE c.id = ' . session('user')->id);
        }
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.schedule_id_edit',
            [
                "title" => (!empty($title->title)) ? $title->title : 'View Count',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data,
                'count1' => count($k),
            ]
        );
    }
    public function schedule_id_edit_frame($id)
    {
        try {
            ini_set('memory_limit', '700M');
            $title = SystemPage::where('name', 'schedule_id_edit')->first();
            $doctor_data = state::where('id', $id)->first();
            $compony_details = ComponyDetail::where('id', '1')->first();

            $fileName = uniqid() . '_' . time() . '.png';

            // Path to the frame image
            $framePath = public_path("reports//mx.png");
            $frame = imagecreatefrompng($framePath);

            // Path to the photo image (ensure it's in PNG format)
            $photoPath1 = $doctor_data->upload_report;
            $imageData1 = file_get_contents(public_path('reports/'.$photoPath1));
            $photo = imagecreatefromstring($imageData1);

            // Get the dimensions of the frame image
            $frameWidth = imagesx($frame);
            $frameHeight = imagesy($frame);

            // Resize the photo image to fit into the frame
            $photoWidth = $frameWidth * 0.17;
            $photoHeight = $frameHeight * 0.17;
            $resizedPhoto = imagescale($photo, $photoWidth, $photoHeight);

            // Create a blank image to serve as the mask
            $mask = imagecreatetruecolor($photoWidth, $photoHeight);
            $maskTransparent = imagecolorallocate($mask, 0, 0, 0);
            imagecolortransparent($mask, $maskTransparent);

            // Calculate the radius of the circular mask
            $radius = min($photoWidth, $photoHeight) / 2;

            // Apply the circular mask
            imagefilledellipse($mask, $photoWidth / 2, $photoHeight / 2, $radius * 2, $radius * 2, $maskTransparent);

            // Apply the circular mask to the resized photo
            imagecopymerge($resizedPhoto, $mask, 0, 0, 0, 0, $photoWidth, $photoHeight, 100);

            // Merge the masked photo onto the frame
            imagecopymerge($frame, $resizedPhoto, 1000, -15, 0, 0, $photoWidth, $photoHeight, 100);
            // Handle company details if empty
            // Save the merged image
             // Add text onto the image
             $textColor = imagecolorallocate($frame, 255, 255, 255); // White color
            $text = $doctor_data->name;
            $font = public_path('font/Arial.ttf'); // Path to your font file
            imagettftext($frame, 20, 0, 1043, 320, $textColor, $font, $text);


            imagepng($frame, public_path('Certificate/' . $fileName));

            
            state::where('id', $id)->update([
                'final_report' => $fileName,
            ]);
            
            if (empty($compony_details)) {
                $compony_details['name'] = 'demo';
                $compony_details['developed'] = 'demo';
            }
            $doctor_data = state::where('id', $id)->first();
            return view(
                'WebsitePages.SuperAdmin.schedule_id_edit_frame',
                [
                    "title" => (!empty($title->title)) ? $title->title : 'View Frame',
                    "compony_details" => $compony_details,
                    'doctor_data' => $doctor_data,
                ]
            );
        } catch (\Exception $e) {
            FacadesLog::error('Error updating add_person_page: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function schedule_id_edit_post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "schedule_id" => ["required"],
            "agent" => ["required"],
            "result" => ["required"],
            "agent_contact" => ["required"],
            "agent_schedule_datetime" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $data = SuperAdminSchedule::where('id', $request['schedule_id'])->first();
            if (isset($request['report'])) {
                $file = $request->file('report');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $publicPath = public_path('reports');
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }
                $file->move($publicPath, $fileName);
                if (!empty($data->upload_report)) {
                    $filePath = public_path('reports\\' . $data->upload_report);
                    if (file_exists($filePath)) {
                        try {
                            unlink($filePath);
                        } catch (\Exception $e) {
                            error_log('Error deleting file: ' . $e->getMessage());
                        }
                    }
                }
            } else {
                $fileName = $data->upload_report;
            }
            DB::beginTransaction();
            SuperAdminSchedule::where('id', $request['schedule_id'])->update([
                'agent' => $request['agent'],
                'result' => $request['result'],
                'agent_contact' => $request['agent_contact'],
                'upload_report' => $fileName,
                'status' => 'report upload',
            ]);
            DB::commit();
            return redirect()->route('schedule');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating add_person_page: ' . $e->getMessage());
            return response()->json(['error' => 'Update failed'], 500);
        }
    }
    public function medicine_reminder()
    {
        $title = SystemPage::where('name', 'medicine_reminder')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        if (session('user')->role == 1) {
            $k = DB::select("SELECT *,schedule.id as s_id FROM schedule JOIN doctor ON schedule.doctor_id = doctor.id where schedule.result < 25 AND schedule.result IS NOT NULL AND schedule.result != ''");
        } else {
            $k = DB::select("SELECT *,schedule.id as s_id FROM schedule JOIN doctor ON schedule.doctor_id = doctor.id where  schedule.user_id =" . session('user')->id . " and schedule.result < 25 AND schedule.result  IS NOT NULL AND schedule.result != '' ");
        }
        return view(
            'WebsitePages.SuperAdmin.medicine_reminder',
            [
                "title" => (!empty($title->title)) ? $title->title : 'medicine_reminder',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'schedule' => $k,
            ]
        );
    }
    public function medicine_reminder_get($id)
    {

        $title = SystemPage::where('name', 'schedule')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        $k1 = DB::select("SELECT *,schedule.id as s_id FROM schedule JOIN doctor ON schedule.doctor_id = doctor.id where schedule.result < 25  and schedule.id =$id ");
        $k = DB::select("SELECT *,schedule.id as s_id FROM schedule JOIN doctor ON schedule.doctor_id = doctor.id where schedule.result < 25 AND schedule.result IS NOT NULL AND schedule.result != ''");
        try {
            $response = Http::get('https://thewhatsappmarketing.com/api/send', [
                'number' => $k1[0]->contact,
                'type' => 'text',
                'message' => 'Dear Doctor, Please take your today\'s medicine',
                'instance_id' => '65B654523DFFD',
                'access_token' => '65742a6cedff6',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $m = "WhatsApp message sent successfully!";
            }
        } catch (\Throwable $e) {
            $m = "Failed to send WhatsApp message. Error: ";
        }
        Session::flash('notification', $m);
        return view(
            'WebsitePages.SuperAdmin.medicine_reminder',
            [
                "title" => (!empty($title->title)) ? $title->title : 'medicine_reminder',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'schedule' => $k,
            ]
        );
    }
    public function track()
    {
        $title = SystemPage::where('name', 'track')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        if (session('user')->role == 1) {
            $k = DB::select("SELECT track.*, doctor.*, schedule.*
            FROM track
            JOIN doctor ON track.doctor_id = doctor.id
            JOIN schedule ON track.doctor_id = schedule.doctor_id
            WHERE (track.doctor_id, track.id) IN (
                SELECT doctor_id, MAX(id) AS max_id
                FROM track
                GROUP BY doctor_id
            );");
        } else {
            $k = DB::select("SELECT track.*, doctor.*, schedule.user_id FROM track JOIN doctor ON track.doctor_id = doctor.id JOIN schedule ON track.doctor_id = schedule.doctor_id WHERE (track.doctor_id, track.id) IN ( SELECT doctor_id, MAX(id) AS max_id FROM track GROUP BY doctor_id ) AND schedule.user_id = " . session('user')->id);
        }
        return view(
            'WebsitePages.SuperAdmin.track',
            [
                "title" => (!empty($title->title)) ? $title->title : 'track',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'schedule' => $k,
            ]
        );
    }
    public function track_id($id)
    {
        $title = SystemPage::where('name', 'track')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1, 2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        if (session('user')->role == 1) {
            $k = DB::select("SELECT t.id, t.doctor_id, t.status, t.created_at, t.updated_at, d.name, s.user_id FROM track AS t JOIN doctor AS d ON t.doctor_id = d.id JOIN schedule AS s ON t.doctor_id = s.doctor_id WHERE
            t.doctor_id = $id;");
        } else {
            $k = DB::select("SELECT
            t.id,
            t.doctor_id,
            t.status,
            t.created_at,
            t.updated_at,
            d.name,
            s.user_id
        FROM
            track AS t
        JOIN
            doctor AS d ON t.doctor_id = d.id
        JOIN
            schedule AS s ON t.doctor_id = s.doctor_id
        WHERE
            t.doctor_id = $id
            AND s.user_id =" . session('user')->id . ";");
        }
        return view(
            'WebsitePages.SuperAdmin.track_id',
            [
                "title" => (!empty($title->title)) ? $title->title : 'track',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'schedule' => $k,
            ]
        );
    }
    public function thankyou()
    {
        return view('WebsitePages.SuperAdmin.thankyou');
    }
    public function update_confirmation(Request $request)
    {
        $sp1 = $request->input('sp1');
        $confirm = $request->input('confirm');

        // Update the confirmation in the database
        state::where('id', $sp1)->update(['confirm' => $confirm]);
        return response()->json(['success' => true]);
    }
}
