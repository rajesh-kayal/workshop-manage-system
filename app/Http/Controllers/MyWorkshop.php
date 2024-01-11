<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Workshop;
use App\Models\Workshop_Session;
use App\Models\Category;

class MyWorkshop extends Controller
{
    public function getWorkshops(Request $request)
    {
        // Extracting request parameters
        $empId = $request->input('empid');
        $perPage = $request->input('perPage', 50);
        $pageNo = $request->input('pageNo', 1);
        $searchType = $request->input('searchType');
        $searchText = $request->input('searchText', '');
        $sortValue = $request->input('sortvalue');
        $sort = $request->input('sort', 'ASC');
        $tab = $request->input('tab');
        $dept = $request->input('dept', 'all');

        // Initialize query builder for Workshop table
        $query = Workshop::query();

        // Check if the employee is registered for any workshop
        $isEmployeeRegistered = DB::table('register')
            ->where('employee_id', $empId)
            ->exists();

        if ($isEmployeeRegistered) {
            // If employee is registered, fetch workshop data from workshop_session table
            $query->join('workshop_session', 'workshop.id', '=', 'workshop_session.workshop_id')
                ->where('workshop_session.is_deleted', 0)
                ->where('workshop_session.workshopdate', '>', now())
                ->where('workshop_session.starttime', '>', now())
                ->select('workshop_session.*', 'workshop.*');
        } else {
            // If employee is not registered, fetch workshop data from workshop table
            $query->where('workshop.status', 'active')
                ->where('workshop.deleted_at', null)
                ->where('workshop.begin_registration_date', '>=', now())
                ->select('workshop.*');
        }

        // Additional conditions based on request parameters
        if ($searchType === 'title') {
            $query->where('workshop.title', 'like', '%' . $searchText . '%');
        }

        if ($sortValue === 'date') {
            $query->orderBy('workshop.begin_registration_date', $sort);
        }

        // Apply pagination
        $result = $query->paginate($perPage, ['*'], 'page', $pageNo);

        // Your response data here
        $responseData = $result->items();

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'pagination' => [
                'totalCount' => $result->total(),
                'totalPage' => $result->lastPage(),
            ],
        ]);
    }
}
