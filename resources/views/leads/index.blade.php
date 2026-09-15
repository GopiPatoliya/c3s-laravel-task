@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lead Management</h2>
        <div>
            <a href="#" id="exportBtn" class="btn btn-secondary">Export to CSV</a>
            <a href="{{ route('leads.create') }}" class="btn btn-primary">Add New Lead</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form id="filterForm" class="row gx-3 gy-2 align-items-center">
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="search" placeholder="Search name, email, phone">
                </div>
                <div class="col-sm-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="converted">Converted</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <select class="form-select" id="sourceFilter">
                        <option value="">All Sources</option>
                        <option value="website">Website</option>
                        <option value="referral">Referral</option>
                        <option value="social_media">Social Media</option>
                        <option value="cold_call">Cold Call</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0" id="leadsTable">
            <thead class="table-light">
                <tr>
                    <th>Sr. No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Source</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="leadsBody">
                <tr><td colspan="7" class="text-center">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    
    <div id="pagination" class="d-flex justify-content-center"></div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="actionToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    
    window.fetchLeads = function(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const status = document.getElementById('statusFilter').value;
        const source = document.getElementById('sourceFilter').value;
        
        const params = new URLSearchParams({
            page: page,
            search: search,
            status: status,
            source: source
        });

        // Update Export button link
        document.getElementById('exportBtn').href = "{{ route('leads.export') }}?" + params.toString();

        // Explicit loading indicator
        document.getElementById('leadsBody').innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

        axios.get("{{ route('leads.data') }}?" + params.toString())
            .then(response => {
                const data = response.data;
                renderTable(data.data, data.from);
                renderPagination(data);
            })
            .catch(error => {
                let errorMsg = error.message;
                if(error.response && error.response.data) {
                    errorMsg += ' | ' + (error.response.data.message || JSON.stringify(error.response.data));
                } else if(error.response) {
                    errorMsg += ' | Status: ' + error.response.status;
                }
                document.getElementById('leadsBody').innerHTML = `<tr><td colspan="7" class="text-danger text-center">Failed to load data. Error: ${errorMsg}</td></tr>`;
            });
    }

    function renderTable(leads, from) {
        let html = '';
        if(leads.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No leads found.</td></tr>';
        } else {
            let serial = from || 1;
            leads.forEach(lead => {
                // Format Source: replace _ with space, capitalize first letter of each word
                let formattedSource = lead.source ? lead.source.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') : '';

                html += `
                <tr id="row-${lead.id}">
                    <td>${serial++}</td>
                    <td>${lead.name}</td>
                    <td>${lead.email || ''}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-link p-0 text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                ${lead.status_badge}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.updateStatus(${lead.id}, 'new')">New</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.updateStatus(${lead.id}, 'contacted')">Contacted</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.updateStatus(${lead.id}, 'converted')">Converted</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.updateStatus(${lead.id}, 'lost')">Lost</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>${formattedSource}</td>
                    <td>${lead.assigned_user ? lead.assigned_user.name : 'Unassigned'}</td>
                    <td>
                        <a href="/leads/${lead.id}" class="btn btn-sm btn-info">View</a>
                        <a href="/leads/${lead.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                        ${lead.can_delete ? `<button class="btn btn-sm btn-danger" onclick="deleteLead(${lead.id})">Delete</button>` : ''}
                    </td>
                </tr>`;
            });
        }
        document.getElementById('leadsBody').innerHTML = html;
    }

    function renderPagination(data) {
        if (data.data.length === 0) {
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        let html = '<nav><ul class="pagination">';
        
        data.links.forEach(link => {
            let label = link.label;
            if(label.includes('Previous')) label = '&laquo;';
            if(label.includes('Next')) label = '&raquo;';
            
            if (link.url) {
                // Extract page from URL robustly
                const urlObj = new URL(link.url, window.location.origin);
                const page = urlObj.searchParams.get('page') || 1;
                html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="window.fetchLeads(${page})">${label}</a></li>`;
            } else {
                html += `<li class="page-item disabled"><a class="page-link" href="javascript:void(0)" tabindex="-1" aria-disabled="true">${label}</a></li>`;
            }
        });
        
        html += '</ul></nav>';
        document.getElementById('pagination').innerHTML = html;
    }

    function showToast(message, isSuccess = true) {
        const toastEl = document.getElementById('actionToast');
        const toastMsg = document.getElementById('toastMessage');
        toastEl.className = `toast align-items-center text-white border-0 ${isSuccess ? 'bg-success' : 'bg-danger'}`;
        toastMsg.innerText = message;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    window.deleteLead = function(id) {
        if(confirm('Are you sure you want to delete this lead?')) {
            axios.delete(`/leads/${id}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => {
                showToast(response.data.message, true);
                fetchLeads(currentPage);
            })
            .catch(error => {
                if(error.response && error.response.status === 403) {
                    showToast('You are not authorized to delete this lead.', false);
                } else {
                    showToast('Failed to delete lead.', false);
                }
            });
        }
    }

    window.updateStatus = function(id, status) {
        axios.patch(`/leads/${id}/status`, { status: status }, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
        .then(response => {
            showToast('Status updated successfully.', true);
            fetchLeads(currentPage);
        })
        .catch(error => {
            showToast('Failed to update status.', false);
        });
    }

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchLeads(1);
    });

    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchLeads(1), 300);
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        fetchLeads(1);
    });

    document.getElementById('sourceFilter').addEventListener('change', function() {
        fetchLeads(1);
    });

    document.getElementById('resetBtn').addEventListener('click', function() {
        document.getElementById('search').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sourceFilter').value = '';
        fetchLeads(1);
    });

    // Initial fetch
    fetchLeads();
});
</script>
@endsection
