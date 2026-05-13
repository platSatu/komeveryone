<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 mr-3">
                    <i class="fas fa-user icon-shadow" style="font-size: 3rem;"></i>
                </div>
                <div>
                    <h5 class="card-title">Users</h5>
                    <h3 class="card-text fs-4 fw-bold mb-0">{{ number_format($totalUsers) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 mr-3">
                    <i class="fas fa-book icon-shadow" style="font-size: 3rem;"></i>
                </div>
                <div>
                    <h5 class="card-title">Kelas Tutorial</h5>
                    <h3 class="card-text fs-4 fw-bold mb-0">{{ number_format($totalActiveKmdClassTutorial) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 mr-3">
                    <i class="fas fa-donate icon-shadow" style="font-size: 3rem;"></i>
                </div>
                <div>
                    <h5 class="card-title">Persembahan</h5>
                    <h3 class="card-text fs-4 fw-bold mb-0">Rp {{ number_format($totalDonations, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>