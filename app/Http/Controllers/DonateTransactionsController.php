<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonateTransactions;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDonationMail;

class DonateTransactionsController extends Controller
{
    public function getAllIndex()
    {
        if (request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to get a user."], 401);
        }

        $donateTransactions = DonateTransactions::orderByDesc('created_at')->paginate(10);
        return response()->json($donateTransactions);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $month = $request->input('month');
        $year = $request->input('year');

        $query = DonateTransactions::orderByDesc('created_at');

        if ($month && $year) {
            $query->whereYear('created_at', $year)->whereMonth('created_at', \Carbon\Carbon::parse($month)->month);
        }

        $donate = $query->paginate($perPage);

        // Calculate total amount
        $totalAmount = intval(DonateTransactions::sum('amount'));

        // Calculate total amount for the specific month and year
        $totalAmountPerMonthAndYear = intval($query->sum('amount'));

        // Count total users
        $totalUsers = DonateTransactions::count();


        $results = DB::table('donate_transactions')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as amount'))
            ->whereYear('created_at', $year)->whereMonth('created_at', \Carbon\Carbon::parse($month)->month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $transactions = [];
        foreach ($results as $result) {
            $transactions[] = [
                'date' => $result->date,
                'amount' => $result->amount,
            ];
        }

        // Create a response array with the data
        $responseData = [
            'data' => $donate->items(),
            'total_amount' => $totalAmount,
            'total_amount_per_month_and_year' => $totalAmountPerMonthAndYear,
            'total_user_donations' => $totalUsers,
            'current_page' => $donate->currentPage(),
            'last_page' => $donate->lastPage(),
            'transactions' => $transactions
        ];

        return response()->json($responseData);
    }

    public function show($id)
    {
        try {
            // Try to find the Donate model with the given ID.
            $donate = DonateTransactions::findOrFail($id);
            return response()->json($donate);
        } catch (ModelNotFoundException $exception) {
            // If the model is not found, throw an error response.
            return response()->json(['error' => 'Donate not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string',
            //Philippines phone number
            'phone' => 'required|string|max:20|min:11',
            'email' => 'required|email',
            'location' => 'required|string',
            'payment_method' => 'required',
            'amount' => 'required|integer|min:1',
            'screenshot_img' => 'required|image',
        ]);

        $image = $request->file('screenshot_img');

        // Read the contents of the file and convert it to a blob
        $ss = file_get_contents($image->getPathname());
        $ss = base64_encode($ss);

        // Create a new donation transaction record to the database
        DonateTransactions::create([
            "user_id" => $user->id,
            "fullname" => $request->fullname,
            "phone" => $request->phone,
            "email" => $request->email,
            "location" => $request->location,
            "payment_method" => $request->payment_method,
            "amount" => $request->amount,
            "screenshot_img" => $ss,
        ]);

        $userData = [
            "fullname" => $request->fullname,
            "phone" => $request->phone,
            "email" => $request->email,
            "location" => $request->location,
            "payment_method" => $request->payment_method,
            "amount" => $request->amount,
            "screenshot_img" => $ss,
        ];

        Mail::to($request->email)->send(new SendDonationMail($userData));

        return response()->json([
            'success' => true,
            'message' => 'Donation successfully recorded! Thank you for your donation to our ministry! 😊',
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public function update(Request $request, $id)
    {
        $transac = DonateTransactions::find($id);

        // Check if the authenticated user is authorized to update the profile
        if (Auth::user()->id !== $transac->user_id) {
            return response()->json(['message' => 'Permission denied'], 401);
        }

        $requestData = $request->all();

        $transac->update($requestData);

        return response()->json([
            'data' => $transac,
            'message' => 'Data updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $isAdmin = request()->input('role') == 'admin' && $user->type == 'admin';

        try {
            $donate = DonateTransactions::findOrFail($id);

            if ($isAdmin || $donate->user_id === $user->id) {
                $donate->delete();
                return response()->json(['message' => 'Donation Transaction deleted successfully']);
            } else {
                return response()->json(['message' => "You don't have permission to delete this transaction."], 401);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Donate Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }

    public function destroyAll()
    {
        $user = Auth::user();

        try {
            $donate = DonateTransactions::where("user_id", $user->id)->get();

            $donate->each->delete();

            return response()->json(['message' => "All Donation's Transactions deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Donation\'s Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }

}
