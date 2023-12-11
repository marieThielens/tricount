<!DOCTYPE html>
<html lang="en">

<head>
    <title>Session1</title>
    <base href="<?= $web_root ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="lib/jquery-3.6.3.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .button-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
    </style>
</head>

<body>
<nav class="navbar  fixed-top  navbar-expand-lg" style="background-color: #e3f2fd;">
    <div class="container-fluid">
        <a class="btn btn-sm btn-outline-danger" type="button" href="user">Back</a>
        <span class="navbar-text"><b>Session 1</b></span>
    </div>
</nav>
<div class="pt-5 pb-3"></div>
<div class="main pb-2">
    <form method="post">
        <div class="row">
            <div class="col-9">
                <select class="form-select">
                    <option>-- Select a User --</option>
                    <option>User1</option>
                    <option>User2</option>
                    <option>User3</option>
                </select>
            </div>
            <div class="col-3">
                <button class="btn btn-outline-secondary" type="submit">Show</button>
            </div>
        </div>
        <div class="form-label mt-2">Participates in these tricounts</div>
        <select size=5 class="form-select">
            <option>Tricount1</option>
            <option>Tricount2</option>
        </select>
        <div class="col m-2 p-0 button-container">
            <button class="btn btn-outline-secondary" type="button" disabled>
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
        <div class="form-label mt-2">Does not participate in these tricounts</div>
        <select size=5 class="form-select">
            <option>Tricount3</option>
            <option>Tricount4</option>
        </select>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>