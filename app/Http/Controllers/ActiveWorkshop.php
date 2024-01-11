<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Workshop;
use App\Models\Workshop_Session;
use App\Models\Category;

class ActiveWorkshop extends Controller
{
    public function activeWorkshops(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empid' => 'required|numeric',
            'perPage' => 'sometimes|numeric|min:1',
            'pageNo' => 'sometimes|numeric|min:1',
            'searchType' => 'sometimes|in:title,other_type',
            'sortvalue' => 'sometimes|in:date,other_value',
            'sort' => 'sometimes|in:ASC,DESC',
            'tab' => 'sometimes|in:nextmonth,other_tab',
            'dept' => 'sometimes|in:all,other_department',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $empId = $validatedData['empid'];
        $perPage = $validatedData['perPage'] ?? 50;
        $pageNo = $validatedData['pageNo'] ?? 1;
        $searchType = $validatedData['searchType'] ?? 'title';
        $searchText = $validatedData['searchText'] ?? '';
        $sortValue = $validatedData['sortvalue'] ?? 'date';
        $sort = $validatedData['sort'] ?? 'ASC';
        $tab = $validatedData['tab'] ?? 'nextmonth';
        $dept = $validatedData['dept'] ?? 'all';

        $offset = ($pageNo - 1) * $perPage;

        $whereClause = "reg.employee_id = $empId AND ws.end_registration_date >= NOW()";

        if (!empty($searchText)) {
            $whereClause .= " AND (ws.title LIKE '%$searchText%')";
        }

        $query = "
        SELECT
            workshop.id AS id,
            workshop.title AS title,
            workshop.department AS department,
            workshop.link AS link,
            workshop.contentarea AS contentarea,
            workshop.description AS description,
            workshop.presenter AS presenter,
            workshop.contactinfo AS contactinfo,
            workshop.location AS location,
            workshop.audiance AS audiance,
            workshop.grade AS grade,
            workshop.series AS series,
            workshop.image AS image,
            workshop.mode AS mode,
            workshop.link AS platform,
            workshop.begin_registration_date AS begin_registration_date,
            workshop.end_registration_date AS end_registration_date,
            workshop.status AS status,
            workshop.created_at AS created_date,
            workshop.created_by AS created_by,
            workshop.created_at AS created_at,
            workshop.updated_at AS updated_at,
            workshop.deleted_at AS deleted_at,
            workshop_session.session_id AS session_id,
            workshop_session.workshop_id AS workshop_id,
            workshop_session.maxcapacity AS maxcapacity,
            workshop_session.workshopdate AS workshopdate,
            workshop_session.lastregdate AS lastregdate,
            workshop_session.starttime AS starttime,
            workshop_session.endtime AS endtime,
            workshop_session.is_deleted AS is_deleted,
            workshop_session.is_deleted AS ord,
            workshop_session.session_id AS sessionid,
            category.category_name AS category_name,
            category.id AS departmentid,
            reg.status AS reg_status,
            reg.id AS regid,
            0 AS reg_count,
            'not' AS registrationStatus,
            workshop_session.maxcapacity AS availableslot,
            DATE_FORMAT(workshop_session.starttime, '%h:%i %p') AS start1,
            DATE_FORMAT(workshop_session.endtime, '%h:%i %p') AS end1,
            DATE_FORMAT(workshop_session.workshopdate, '%Y-%m-%d') AS date1,
            DATE_FORMAT(workshop_session.endtime, '%H:%i') AS end2,
            DATE_FORMAT(workshop_session.created_at, '%m-%d-%Y') AS being_registration_datenew,
            DATE_FORMAT(workshop_session.updated_at, '%m-%d-%Y') AS end_registration_datenew,
            DATE_FORMAT(workshop_session.workshopdate, '%m-%d-%Y') AS workshopdatenew,
            TIMEDIFF(workshop_session.endtime, workshop_session.starttime) AS timediff,
            CONCAT(workshop_session.workshopdate, ' ', workshop_session.endtime) AS sessiontime,
            NOW() AS now,
            DATE_FORMAT(workshop_session.workshopdate, '%m-%d-%Y') AS workshopdatenew,
            CONCAT(
                FLOOR(TIME_TO_SEC(TIMEDIFF(workshop_session.endtime, workshop_session.starttime)) / 3600), ' Hours ',
                MOD(FLOOR(TIME_TO_SEC(TIMEDIFF(workshop_session.endtime, workshop_session.starttime)) / 60), 60), ' Minutes'
            ) AS timediff,
            workshop.department AS department,

            CASE
                WHEN workshop_session.is_deleted = 1 THEN 'Deleted'
                WHEN workshop_session.lastregdate < NOW() THEN 'Closed'
                WHEN workshop_session.workshopdate > NOW() THEN 'Active'
                ELSE 'Scheduled'
            END AS sst

        FROM
            workshop
        JOIN workshop_session ON workshop.id = workshop_session.workshop_id
        JOIN category ON workshop.department = category.id
        LEFT JOIN register AS reg ON workshop.id = reg.workshop_id AND workshop_session.session_id = reg.session_id
        WHERE
            workshop.created_by = 21463
            AND workshop_session.workshopdate > NOW()
            AND (workshop.title LIKE '' OR 'title' = 'title')
            AND (workshop.department = 'all' OR 'all' = 'all')
        ORDER BY
            CASE
                WHEN 'date' = 'date' AND 'ASC' = 'ASC' THEN workshop_session.workshopdate
                WHEN 'date' = 'date' AND 'DESC' = 'DESC' THEN workshop_session.workshopdate
                ELSE workshop_session.workshopdate
            END
            LIMIT 2 OFFSET $offset;";

        $workshops = DB::select($query);

$totalCountQuery = "
    SELECT COUNT(DISTINCT ws.id) AS total
    FROM workshop AS ws
    JOIN workshop_session AS ses ON ws.id = ses.workshop_id
    JOIN category AS cat ON ws.department = cat.id
    LEFT JOIN register AS reg ON ws.id = reg.workshop_id AND ses.session_id = reg.session_id
    WHERE $whereClause";

$totalCountResult = DB::select($totalCountQuery);

//$totalCount = $totalCountResult[0]->total;
$totalCount = count($workshops);

$totalPage = ceil($totalCount / $perPage);


$response = [
    'status' => true,
    'data' => $workshops,
    'pagination' => [
        'totalCount' => $totalCount,
        'totalPage' => $totalPage,
    ],
];

return response()->json($response);

    }
}

//starat date and enddate not workshop date