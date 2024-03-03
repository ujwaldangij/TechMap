<?php

namespace App\Http\Controllers\WebsiteController\SuperAdmin;

use Log;
use Illuminate\Http\Request;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\WebsiteModels\SuperAdmin\menu;
use App\Models\WebsiteModels\SuperAdmin\Credential;
use App\Models\WebsiteModels\SuperAdmin\SystemPage;
use App\Models\WebsiteModels\SuperAdmin\ComponyDetail;
use App\Models\WebsiteModels\SuperAdmin\doctor as SuperAdminDoctor;
use App\Models\WebsiteModels\SuperAdmin\Notification as SuperAdminNotification;
use App\Models\WebsiteModels\SuperAdmin\schedule;
use App\Models\WebsiteModels\SuperAdmin\track;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Validator;
use App\Models\WebsiteModels\SuperAdmin\schedule as SuperAdminSchedule;
use App\Models\WebsiteModels\SuperAdmin\state as SuperAdminState;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class doctor extends Controller
{
    public function index()
    {
        $title = SystemPage::where('name', 'doctor')->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        $menu2 = new menu();
        $user =  session('user')->role;
        if ($user  == 2 || $user == 5) {
            $menu = menu::whereIn('id', [1,2, 3, 4])->get();
        } elseif ($user  == 3) {
            $menu = menu::whereIn('id', [2])->get();
        } else {
            $menu = new menu();
        }
        $notifications = SuperAdminNotification::all();
        return view(
            'WebsitePages.SuperAdmin.doctor',
            [
                "title" => (!empty($title->title)) ? $title->title : 'doctor',
                "compony_details" => $compony_details,
                "menu" => $menu->all(),
                "sub_menu" => $menu2->getMenuWithSubmenus(),
                'notifications' => $notifications,
                'doctor' => SuperAdminDoctor::where('align', 'yes')->get(),
            ]
        );
    }
    public function choose($id)
    {
        $title = SystemPage::where('name', 'login')->first();
        $doctor_data = SuperAdminDoctor::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.choose',
            [
                "title" => (!empty($title->title)) ? $title->title : 'Choose Doctor Id',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data,
            ]
        );
    }
    public function postchooseid(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "esign" => ["required"],
            "Sample_Collection_Addresh" => ["required"],
            "schedule_time" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            // Use a transaction to ensure data consistency
            DB::beginTransaction();

            // Your update logic here
            SuperAdminDoctor::where('id', $request['doctor_id'])->update([
                'align' => 'yes',
                'esign' => $request['esign'],
                'address' => $request['Sample_Collection_Addresh'],
                'schedule_time' => $request['schedule_time'],
            ]);
            track::create([
                'status' => 'schedule',
                'doctor_id' => $request['doctor_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            schedule::create([
                'doctor_id' => $request['doctor_id'],
                'status' => 'schedule',
                'agent' => '',
                'result' => '',
                'upload_report' => '',
                'user_id' => session('user')->id,
            ]);
            // Commit the transaction if everything is successful
            DB::commit();

            // Success response or redirection
            return redirect()->route('choose_doctor');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating SuperAdminDoctor: ' . $e->getMessage());
            return response()->json(['error' => 'Update failed'], 500);
        }
    }
    public function choose_id_delete($id)
    {
        // dd('yes');
        try {
            // Use a transaction to ensure data consistency
            DB::beginTransaction();
            SuperAdminDoctor::where('id', $id)->delete();
            DB::commit();
            return redirect()->route('choose_doctor');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error delete SuperAdminDoctor: ' . $e->getMessage());
            return response()->json(['error' => 'Update delete'], 500);
        }
    }
    public function choose_id_edit($id)
    {
        $title = SystemPage::where('name', 'login')->first();
        $doctor_data = SuperAdminDoctor::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.editchoose',
            [
                "title" => (!empty($title->title)) ? $title->title : 'Choose Doctor Id',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data,
            ]
        );
    }
    public function choose_id_edit_post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "Doctor_name" => ["required"],
            "Doctor_contact" => ["required"],
            "Doctor_email" => ["required"],
            "specialties" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            // Use a transaction to ensure data consistency
            DB::beginTransaction();

            // Your update logic here
            SuperAdminDoctor::where('id', $request['doctor_id'])->update([
                'align' => 'no',
                'esign' => '',
                'name' => $request['Doctor_name'],
                'contact' => $request['Doctor_contact'],
                'email' => $request['Doctor_email'],
                'specialties' => $request['specialties'],
            ]);
            DB::commit();

            // Success response or redirection
            return redirect()->route('choose_doctor');
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            // Log the exception for debugging purposes
            FacadesLog::error('Error updating SuperAdminDoctor: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Update failed'], 500);
        }
    }
    public function fill_form()
    {
        $title = SystemPage::where('name', 'fill_form')->first();
        // $doctor_data = SuperAdminSchedule::where('id', $id)->first();
        $compony_details = ComponyDetail::where('id', '1')->first();
        if (empty($compony_details)) {
            $compony_details['name'] = 'demo';
            $compony_details['developed'] = 'demo';
        }
        return view(
            'WebsitePages.SuperAdmin.fill_form',
            [
                "title" => (!empty($title->title)) ? $title->title : 'fill_form',
                "compony_details" => $compony_details,
                'doctor_data' => $doctor_data = "",
            ]
        );
    }
    public function fill_form_post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "specialty" => ["required"],
            "doctor_name" => ["required"],
            "report" => ["required"],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $file = $request->file('report');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $publicPath = public_path('reports');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            $file->move($publicPath, $fileName);
            DB::beginTransaction();
            $k = SuperAdminState::create([
                'name' => $request['doctor_name'],
                'specialty' => $request['specialty'],
                'created_id' => session('user')->id,
                'upload_report' => $fileName,
            ]);
            session(['sp1' => $k->id]);
            DB::commit();
            return redirect()->route('map');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating fill_form_post: ' . $e->getMessage());
            return back()->withErrors(['issue' => 'fill_form_post failed '])->withInput();
        }
    }
    public function map()
    {
        return view('WebsitePages.SuperAdmin.map',['data' =>SuperAdminState::all()]);
    }
}
