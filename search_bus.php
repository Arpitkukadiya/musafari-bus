<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'customer') {
    header("Location: index.php");
    exit;
}

$today = date('Y-m-d'); // Get today's date in the format 'YYYY-MM-DD'
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    body {
      background-color: #f8f9fa;
    }
.form-container{
  max-width: 500px;
  margin: 0 auto;
}
    .navbar {
      background-color: #1e63d4;
    }

    .navbar a {
      color: white;
    }

    .card {
      display: flex;
      justify-content: center;
      margin-top: 50px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 30px;
    }

    .section-title {
      color: #1e63d4;
      margin-top: 30px;
      font-weight: bold;
    }

    .form-control,
    .btn {
      border-radius: 10px;
    }

    .btn-success {
      background-color: #1e63d4;
      color: white;
      border-radius: 10px;
      width: 100%;
      border: none;
      font-size: 1rem;
    }

    .btn-success:hover {
      background-color: #1e63d4;
      color: white;
    }

    .form-control {
      border-radius: 10px;
      box-shadow: inset 0 1px 5px rgba(0, 0, 0, 0.1);
      margin-top: 5px;
    }

    .suggestions {
      position: absolute;
      z-index: 1000;
      background: white;
      border: 1px solid #ddd;
      max-height: 150px;
      overflow-y: auto;
    }

    .suggestions div {
      padding: 8px;
      cursor: pointer;
    }

    .suggestions div:hover {
      background-color: #f0f0f0;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<?php include "navbar.php" ?>

<div class="container mt-5">
  <div class="form-container">
    <div class="card px-3">
      <h3 class="section-title text-center mb-4 pb-2">Search & Book Tickets</h3>

      <form method="post" action="available_buses.php">
        <div class="row">
          <!-- Source Field -->
          <div class="col-md-12 col-12 mb-3">
            <label for="source" class="form-label">Source</label>
            <input type="text" class="form-control" id="source" name="source" placeholder="Source" required>
            <div id="source-suggestions" class="suggestions"></div>
          </div>

          <!-- Destination Field -->
          <div class="col-md-12 col-12 mb-3">
            <label for="destination" class="form-label">Destination</label>
            <input type="text" class="form-control" id="destination" name="destination" placeholder="Destination" required>
            <div id="destination-suggestions" class="suggestions"></div>
          </div>

          <!-- Travel Date Field -->
          <div class="col-md-12 col-12 mb-3">
            <label for="travel_date" class="form-label">Travel Date</label>
            <input type="date" class="form-control" id="travel_date" name="travel_date" placeholder="Travel Date" min="<?= $today ?>" required>
          </div>

          <!-- No of Passengers Field -->
          <div class="col-md-12 col-12 mb-3">
            <label for="passengers" class="form-label">No of Passengers</label>
            <input type="number" class="form-control" id="passengers" name="passengers" min="1" placeholder="No of Passengers" required>
          </div>

          <!-- Search Button -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary w-100 p-2 mt-1 mb-3">Search Bus</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Function to fetch suggestions
  function fetchSuggestions(inputId, suggestionsId, type) {
    $(inputId).on("input", function() {
      const query = $(this).val();
      if (query.length > 0) {
        $.ajax({
          url: "fetch_suggestions.php",
          method: "POST",
          data: { query: query, type: type },
          success: function(data) {
            $(suggestionsId).html(data).show();
          }
        });
      } else {
        $(suggestionsId).hide();
      }
    });

    // Set the input field value on click of suggestion
    $(document).on("click", suggestionsId + " div", function() {
      $(inputId).val($(this).text());
      $(suggestionsId).hide();
    });
  }

  // Fetch suggestions for source and destination
  fetchSuggestions("#source", "#source-suggestions", "source");
  fetchSuggestions("#destination", "#destination-suggestions", "destination");
});
</script>

</body>
</html>
