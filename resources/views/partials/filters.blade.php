<div class="card card-outline card-primary">
    <div class="card-header"><h2 class="card-title">Filters</h2></div>
    <div class="card-body">
        <form method="GET" class="row">
            @if(isset($searchable) && $searchable)
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            @endif
            @if(isset($statusFilter) && $statusFilter)
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paying" {{ request('status') == 'paying' ? 'selected' : '' }}>Paying</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            @endif
            @if(isset($dateFilter) && $dateFilter)
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
            </div>
            @endif
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                
            </div>
        </form>
    </div>
</div>
