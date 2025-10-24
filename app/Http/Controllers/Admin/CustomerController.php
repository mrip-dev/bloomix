<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;

class CustomerController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Customers';
    }
    protected function getCustomers()
    {
        return Customer::searchable(['name', 'mobile', 'email', 'address'], false)->with('sale', 'saleReturns')->orderBy('name', 'asc');
    }
    public function index()
    {
        $pageTitle = $this->pageTitle;
        $customers = $this->getCustomers()->paginate(getPaginate());
        return view('admin.customer.list', compact('pageTitle', 'customers'));
    }

    public function customerPDF()
    {
        $pageTitle = $this->pageTitle;
        $customers = $this->getCustomers()->get();
        return downloadPDF('pdf.customer.list', compact('pageTitle', 'customers'));
    }
    public function customerCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getCustomers()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "name,email,mobile,address\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $customer) {
            // $receivable = $curSym . getAmount($customer->totalReceivableAmount());
            // $payable    = $curSym . getAmount($customer->totalPayableAmount());
            $cleanAddress = str_replace(',', '', $customer->address);
            $column .= "$customer->name,$customer->email,$customer->mobile,$cleanAddress\n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request, $id);
        if ($id) {
            $notification = 'Customer updated successfully';
            $customer     = Customer::findOrFail($id);
        } else {
            // $exist = Customer::where('mobile', $request->mobile)->first();
            // if ($exist) {
            //     $notify[] = ['error', 'The mobile number already exists'];
            //     return back()->withNotify($notify);
            // }
            $notification = 'Customer added successfully';
            $customer     = new Customer();
        }

        $customer->name    = $request->name;
        $customer->email   = strtolower(trim($request->email));
        $customer->mobile  = $request->mobile;
        $customer->address = $request->address;
        $customer->opening_balance = $request->opening_balance ?? 0.00;
        $customer->booklet_no = $request->booklet_no;
        $customer->advance = $request->advance ?? 0.00; // Ensure advance is set, default to 0.00

        $customer->save();

        Action::newEntry($customer, $id ? 'UPDATED' : 'CREATED');

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Optional: check if customer has related sales or constraints to prevent deletion
        // if ($customer->sale()->exists() || $customer->saleReturns()->exists()) {
        //     $notify[] = ['error', 'Cannot delete customer with associated sales or returns'];
        //     return back()->withNotify($notify);
        // }

        $customer->delete();

        Action::newEntry($customer, 'DELETED');

        $notify[] = ['success', 'Customer deleted successfully'];
        return back()->withNotify($notify);
    }

    protected function validation($request, $id = 0)
    {
        $request->validate([
            'name'    => 'required|string|max:40',
            'booklet_no'    => 'required|string|max:100',
            'email'   => 'nullable',
            'mobile'  => 'nullable',
            'address' => 'string|max:500',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);
    }

    public function list()
    {
        $customers = Customer::query();
        if (request()->search) {
            $customers->where(function ($q) {
                $q->where('email', 'like', '%' . request()->search . '%')->orWhere('username', 'like', '%' . request()->search . '%');
            });
        }

        $users = $customers->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'users'   => $users,
            'more'    => $users->hasMorePages()
        ]);
    }




    public function showNotificationSingleForm($id)
    {
        $customer = Customer::findOrFail($id);
        if (!gs('en') && !gs('sn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return back()->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $customer->name;
        return view('admin.customer.notification_single', compact('pageTitle', 'customer'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
            'via'     => 'required|in:email,sms',
            'subject' => 'required_if:via,email',
        ]);

        if (!gs('en') && !gs('sn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }


        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via . '_status', Status::ENABLE)->exists();
        if (!$template) {
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        $customer = Customer::findOrFail($id);
        notify($customer, 'DEFAULT', [
            'subject' => $request->subject,
            'message' => $request->message,
        ], [$request->via]);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }



    public function showNotificationAllForm()
    {
        if (!gs('en') && !gs('sn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToUser = Customer::notifyToUser();
        $users        = Customer::count();
        $pageTitle    = 'Notification to Customer';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.customer.notification_all', compact('pageTitle', 'users', 'notifyToUser'));
    }

    public function sendNotificationAll(Request $request)
    {
        $request->validate([
            'via'           => 'required|in:email,sms,push',
            'message'       => 'required',
            'subject'       => 'required_if:via,email,push',
            'start'         => 'required|integer|gte:1',
            'batch'         => 'required|integer|gte:1',
            'being_sent_to' => 'required',
            'cooling_time'  => 'required|integer|gte:1',
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }


        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via . '_status', Status::ENABLE)->exists();
        if (!$template) {
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        if ($request->being_sent_to == 'selectedUsers') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['user' => session()->get('SEND_NOTIFICATION')['user']]);
            } else {
                if (!$request->user || !is_array($request->user) || empty($request->user)) {
                    $notify[] = ['error', "Ensure that the user field is populated when sending an email to the designated user group"];
                    return back()->withNotify($notify);
                }
            }
        }

        $scope     = $request->being_sent_to;
        $userQuery = Customer::oldest()->$scope();

        if (session()->has("SEND_NOTIFICATION")) {
            $totalUserCount = session('SEND_NOTIFICATION')['total_user'];
        } else {
            $totalUserCount = (clone $userQuery)->count() - ($request->start - 1);
        }


        if ($totalUserCount <= 0) {
            $notify[] = ['error', "Notification recipients were not found among the selected user base."];
            return back()->withNotify($notify);
        }




        $users = (clone $userQuery)->skip($request->start - 1)->limit($request->batch)->get();

        foreach ($users as $user) {
            notify($user, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via]);
        }

        return $this->sessionForNotification($totalUserCount, $request);
    }

    private function sessionForNotification($totalUserCount, $request)
    {
        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData                = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData               = $request->except('_token');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_user'] = $totalUserCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalUserCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url     = route("admin.customer.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url     = route("admin.customer.notification.all") . "?email_sent=yes";
        }
        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }



    public function notificationLog($id)
    {
        $customer  = Customer::findOrFail($id);
        $pageTitle = 'Notifications Sent to ' . $customer->name;
        $logs      = NotificationLog::where('customer_id', $id)->with('customer')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.customer.notification_history', compact('pageTitle', 'logs', 'customer'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email     = NotificationLog::findOrFail($id);
        return view('admin.customer.email_details', compact('pageTitle', 'email'));
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name', 'email', 'mobile', 'address'];
        $importResult = importCSV($request, Customer::class, $reqHeader, 'name');

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
