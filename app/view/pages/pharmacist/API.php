<!DOCTYPE html>
<html>
<head>
    <title>Drug Search</title>
    <link rel="stylesheet" href="API.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="search-box">
        <form id="drugForm">
            <input id="drugName" class="form-control" placeholder="Enter drug name...">
            <button class="btn btn-primary mt-2">Search</button>
        </form>
    </div>

    <div id="result"></div>
</div>

<script src="API.js"></script>
</body>
</html>
