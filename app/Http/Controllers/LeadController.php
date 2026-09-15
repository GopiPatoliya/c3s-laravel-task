<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeadController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('leads.index');
    }

    public function data(Request $request)
    {
        $query = Lead::query()->with('assignedUser');

        if (!auth()->user()->is_admin) {
            $query->where('assigned_to', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $leads = $query->latest()->paginate(10);
        $leads->getCollection()->transform(function ($lead) {
            $lead->status_badge = leadStatusBadge($lead->status);
            $lead->can_delete = auth()->user()->can('delete', $lead);
            return $lead;
        });
        return response()->json($leads);
    }

    public function create()
    {
        $users = User::all();
        return view('leads.create', compact('users'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        Lead::create($data);
        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        if (!auth()->user()->is_admin && auth()->id() !== $lead->assigned_to) {
            abort(403, 'Unauthorized action.');
        }

        $lead->load('assignedUser');
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        if (!auth()->user()->is_admin && auth()->id() !== $lead->assigned_to) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all();
        return view('leads.edit', compact('lead', 'users'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        if (!auth()->user()->is_admin && auth()->id() !== $lead->assigned_to) {
            abort(403, 'Unauthorized action.');
        }

        $lead->update($request->validated());
        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();
        return response()->json(['success' => true, 'message' => 'Lead deleted successfully.']);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        if (!auth()->user()->is_admin && auth()->id() !== $lead->assigned_to) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate(['status' => 'required|in:new,contacted,converted,lost']);
        $lead->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function export(Request $request)
    {
        $query = Lead::query();

        if (!auth()->user()->is_admin) {
            $query->where('assigned_to', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $leads = $query->get();

        $filename = "leads_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');
        
        ob_start();
        fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Status', 'Source', 'Assigned To', 'Created At']);
        
        foreach ($leads as $lead) {
            fputcsv($handle, [
                $lead->id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->company_name,
                $lead->status,
                $lead->source,
                $lead->assignedUser ? $lead->assignedUser->name : '',
                $lead->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($handle);
        $csv = ob_get_clean();

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
