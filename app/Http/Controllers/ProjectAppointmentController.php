<?php

namespace App\Http\Controllers;

use App\Models\ProjectAppointment;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\ProjectOrder;
use App\Models\ProjectMaintain;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ProjectAppointmentController extends Controller
{
    public function select2Query(Request $request)
    {
        return ProjectAppointment::when($request->has('search'), function ($query) use ($request) {
            $query->where('project_number', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }

    public function index(Request $request): Response
    {
        $search = $request->search;
        $order = $request->order;
        $by = $request->by;
        $status = $request->status ?? 'default';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;
        $filters['status'] = $status;

        $statusOrder = "CASE
            WHEN status = " . ProjectAppointment::STATUS_ACTIVE . " AND date_of_appointment >= CURDATE() THEN 1
            WHEN status = " . ProjectAppointment::STATUS_CONVERTED . " THEN 2
            WHEN status = " . ProjectAppointment::STATUS_ACTIVE . " AND date_of_appointment < CURDATE() THEN 3
            WHEN status = " . ProjectAppointment::STATUS_VOID . " THEN 4
            WHEN status = " . ProjectAppointment::STATUS_DRAFT . " THEN 5
            ELSE 6 END";

        $appointments = ProjectAppointment::with(['customer', 'vehicle'])
            ->when($search, function ($query) use ($search) {
                $query->where('project_number', 'LIKE', "%{$search}%");
            })
            ->when($status !== 'default', function ($query) use ($status) {
                switch ($status) {
                    case 'upcoming':
                        $query->where('status', ProjectAppointment::STATUS_ACTIVE)
                            ->where('date_of_appointment', '>=', now());
                        break;
                    case 'converted':
                        $query->where('status', ProjectAppointment::STATUS_CONVERTED);
                        break;
                    case 'expired':
                        $query->where('status', ProjectAppointment::STATUS_ACTIVE)
                            ->where('date_of_appointment', '<', now());
                        break;
                    case 'void':
                        $query->where('status', ProjectAppointment::STATUS_VOID);
                        break;
                    default:
                        break;
                }
            })
            ->orderByRaw($statusOrder)
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        $appointments->appends([
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
            'status' => $status,
        ]);

        return Inertia::render('ProjectAppointment/Index', [
            'appointments' => $appointments,
            'filters' => $filters,
        ]);
    }

    public function show(ProjectAppointment $project_appointment): Response
    {
        return Inertia::render('ProjectAppointment/Detail', [
            'appointment' => $project_appointment->load(['customer', 'vehicle']),
        ]);
    }

    public function create(): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');

        return Inertia::render('ProjectAppointment/Form', [
            'csrf' => csrf_token(),
            'phoneCodes' => $phone_codes,
            'types' => ProjectAppointment::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'date_of_appointment' => 'required|date',
            'project_order_type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_email' => 'nullable|email',
            'status' => 'required|integer',
        ]);

        $data = $request->all();
        $data['date_of_appointment'] = Carbon::parse($data['date_of_appointment']);

        $projectAppointment = ProjectAppointment::create($data);

        $projectAppointment->update([
            'project_number' =>
                ($projectAppointment->status == ProjectAppointment::STATUS_ACTIVE ? ProjectAppointment::ACTIVE_PREFIX : ProjectAppointment::DRAFT_PREFIX)
                . str_pad($projectAppointment->id, 4, '0', STR_PAD_LEFT)
        ]);

        return redirect('/project-appointments')->with('created', 'Project appointment created successfully.');
    }

    public function edit(ProjectAppointment $project_appointment): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');

        return Inertia::render('ProjectAppointment/Form', [
            'csrf' => csrf_token(),
            'appointment' => $project_appointment->load(['maintains']),
            'phoneCodes' => $phone_codes,
            'types' => ProjectAppointment::TYPES,
        ]);
    }

    public function update(Request $request, ProjectAppointment $projectAppointment): RedirectResponse
    {
        $request->validate([
            'date_of_appointment' => 'required|date',
            'project_order_type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_email' => 'nullable|email',
            'status' => 'required|integer',
        ]);

        $data = $request->all();
        $data['date_of_appointment'] = Carbon::parse($data['date_of_appointment']);

        $projectAppointment->update($data);

        return redirect('/project-appointments')->with('updated', 'Project appointment updated successfully.');
    }

    public function convertDraft(ProjectAppointment $project_appointment): RedirectResponse
    {
        $project_appointment->update(['status' => 2]);

        $project_appointment->update([
            'project_number' =>
                ($project_appointment->status == ProjectAppointment::STATUS_ACTIVE ? ProjectAppointment::ACTIVE_PREFIX : ProjectAppointment::DRAFT_PREFIX)
                . str_pad($project_appointment->id, 4, '0', STR_PAD_LEFT)
        ]);

        return redirect('/project-appointments')->with('updated', 'Project appointment updated successfully.');
    }

    public function convertProjectOrder(ProjectAppointment $project_appointment): Application|Redirector|RedirectResponse
    {
        $projectOrder = new ProjectOrder();
        $projectOrder->converted_project_appointment_id = $project_appointment->id;
        $projectOrder->customer_feedback = $project_appointment->customer_feedback;
        $projectOrder->project_order_type = $project_appointment->project_order_type;
        $projectOrder->customer_id = $project_appointment->customer_id;
        $projectOrder->vehicle_id = $project_appointment->vehicle_id;
        $projectOrder->pic_first_name = $project_appointment->pic_first_name;
        $projectOrder->pic_last_name = $project_appointment->pic_last_name;
        $projectOrder->pic_email = $project_appointment->pic_email;
        $projectOrder->remark = $project_appointment->remark;
        $projectOrder->status = $project_appointment->status;
        $projectOrder->save();

        $projectOrder->update([
            'code' =>
                ($projectOrder->status == ProjectOrder::STATUS_DRAFT ? ProjectOrder::DRAFT_PREFIX : ProjectOrder::ACTIVE_PREFIX)
                . str_pad($projectOrder->id, 4, '0', STR_PAD_LEFT)
        ]);

        $project_appointment->update(['status' => ProjectAppointment::STATUS_CONVERTED]);

        return redirect('/project-orders')->with('updated', 'Project order created successfully.');
    }

    public function destroy(ProjectAppointment $project_appointment): RedirectResponse
    {
        $project_appointment->update(['status' => ProjectAppointment::STATUS_VOID]);

        return redirect('/project-appointments')->with('deleted', 'Project appointment successfully cancelled');
    }
}
