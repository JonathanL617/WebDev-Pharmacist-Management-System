<?php require_once __DIR__ . '/../../../config/config.php'; ?>

<div class="container py-4">
    <div class="text-center mb-4">
        <h1>Drug Search</h1>
        <p class="text-muted">Search for drug information</p>
    </div>

    <!-- Your exact search box -->
    <div class="search-box">
        <form id="drugForm">
            <div class="row">
                <div class="col-md-8">
                    <label for="drugName" class="form-label">Drug Name</label>
                    <input 
                        type="text" 
                        id="drugName" 
                        class="form-control" 
                        placeholder="Enter drug name" 
                        required
                    >
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div id="result"></div>
</div>

<!-- Your EXACT CSS -->
<style>
    .container { max-width: 800px; }
    .search-box { 
        background: white; 
        padding: 20px; 
        border-radius: 5px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        margin-bottom: 20px; 
    }
    .result-box { 
        background: white; 
        padding: 20px; 
        border-radius: 5px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        margin-bottom: 15_px; 
    }
</style>

<!-- Pass BASE_URL -->
<script>window.BASE_URL = "<?php echo BASE_URL; ?>";</script>

<!-- Load your EXACT JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/API.js"></script>