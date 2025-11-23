<!-- doctor history -->
<div class="tab-content" id="view-history">
    <!-- stat counters -->
    <br>
    <div class="stats-container mb-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            <p class="stat-label">Total Records</p>
            <div class="stat-value" id="totalRecords">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
            <p class="stat-label">Today</p>
            <div class="stat-value" id="todayRecords">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-week"></i></div>
            <p class="stat-label">This Week</p>
            <div class="stat-value" id="thisWeekRecords">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
            <p class="stat-label">This Month</p>
            <div class="stat-value" id="thisMonthRecords">0</div>
        </div>
    </div>
    <h2>History</h2>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search">
            </div>
        </div>
        <div class="col-lg-2 col-md-3 mb-3">
            <select id="filter-date" class="form-select">
                <option value="">All</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
        </div>
    </div>

    <!-- History Table -->
    <div class="responsive-table">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Record ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="historyTable">
                <tr>
                    <td colspan="6" class="text-center text-muted">Loading history...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>