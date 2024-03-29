<?php

namespace App\Response\Manager\api;

use App\Http\Resources\DonateTransactionResource;
use App\Models\DonateTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDonationMail;

class DonateTransactionManagerResponse
{

    public function getAllIndex()
    {
        $pagination = 10;

        if (request()->input("items")) {
            $pagination = request()->input("items");
        }

        if (request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to get a user."], 401);
        }

        $donateTransactions = DonateTransactions::orderBy('id', request()->input("order") ? request()->input("order") : 'desc')
            ->paginate($pagination);

        $data = [
            'current_page' => $donateTransactions->currentPage(),
            'data' => DonateTransactionResource::collection($donateTransactions),
            'first_page_url' => $donateTransactions->url(1),
            'from' => $donateTransactions->firstItem(),
            'last_page' => $donateTransactions->lastPage(),
            'last_page_url' => $donateTransactions->url($donateTransactions->lastPage()),
            'next_page_url' => $donateTransactions->nextPageUrl(),
            'path' => $donateTransactions->path(),
            'per_page' => $donateTransactions->perPage(),
            'prev_page_url' => $donateTransactions->previousPageUrl(),
            'to' => $donateTransactions->lastItem(),
            'total' => $donateTransactions->total(),
        ];
        return response()->json($data);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($request->input('user')) {
            if (intval($request->input('user')) === intval($user->id)) {
                $query = DonateTransactions::where('user_id', $user->id)->orderByDesc('created_at')->get();

                $responseData = [
                    'data' => DonateTransactionResource::collection($query)
                ];

                return response()->json($responseData);
            } else {
                return response()->json([
                    'message' => "You are not authorized to access donation's transactions information",
                ], 403);
            }
        } else {
            $perPage = $request->input('per_page', 10);
            $month = $request->input('month');
            $year = $request->input('year');

            $query = DonateTransactions::orderByDesc('created_at');

            if ($month && $year) {
                $query->whereYear('created_at', $year)->whereMonth('created_at', \Carbon\Carbon::parse($month)->month);
            }

            $donate = $query->paginate($perPage);
            $totalAmount = intval(DonateTransactions::sum('amount'));
            $totalAmountPerMonthAndYear = intval($query->sum('amount'));
            $totalDonations = DonateTransactions::count();

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

            $responseData = [
                'data' => $donate->items(),
                'total_amount' => $totalAmount,
                'total_amount_per_month_and_year' => $totalAmountPerMonthAndYear,
                'total_user_donations' => $totalDonations,
                'current_page' => $donate->currentPage(),
                'last_page' => $donate->lastPage(),
                'transactions' => $transactions
            ];

            return response()->json($responseData);
        }
    }

    public function show($id)
    {
        try {
            $donate = DonateTransactions::findOrFail($id);
            return response()->json($donate);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Donate not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'fullname' => 'required|string',
            'phone' => 'required|string|max:20|min:11',
            'email' => 'required|email',
            'location' => 'required|string',
            'payment_method' => 'required',
            'amount' => 'required|integer|min:1',
            'screenshot_img' => 'nullable|image',
        ]);

        $ss = null;
        if ($request->hasFile('screenshot_img')) {
            $image = $request->file('screenshot_img');
            $ss = file_get_contents($image->getPathname());
            $ss = base64_encode($ss);
        }

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
