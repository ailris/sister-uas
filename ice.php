<?php
include("library.php");

function showUI()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Ice Cream</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    </head>

    <body>
        <div class="jumbotron text-center">
            <h3 class="display-4">Lists of Ice cream Flavors</h3>
            <p class="lead">You can add, delete, and update the ice cream flavors here</p>
            <hr class="my-4">
        </div>
        <!-- Modal -->
        <div class="modal" id="myForm" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="myFormTitle">Add Ice Cream</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ice_name" class="form-label">Ice Cream Flavors</label>
                            <input type="text" class="form-control" id="ice_name" placeholder="New Ice">
                        </div>
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Type</label>
                            <select class="form-select form-control" id="ice_type">
                                <option>Please choose one</option>
                                <option value="Gelato">Gelato</option>
                                <option value="Sorbet">Sorbet</option>
                                <option value="Popsicle">Popsicle</option>
                                <option value="Soft Serve">Soft Serve</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ingredients" class="form-label">Ingredients</label>
                            <textarea class="form-control" id="ingredients" rows="3" placeholder="Insert the Ingredients"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" placeholder="Insert the price">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <p id="feedback"></p>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="save" type="button" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-danger" id="confirm-delete" style="display: none;">Delete Record</button>


                    </div>
                </div>
            </div>
        </div>


        <div class="container py-5">
            <p>
                <button id="add" type="button" class="btn btn-primary btn-sm">Add</button>
            </p>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Ice Cream</th>
                        <th>Type</th>
                        <th>Ingredients</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>

        </div>
        <script>
            var flag = "none";
            var table = $('#example').DataTable({
                serverSide: true,
                ajax: {
                    url: '?flag=show',
                    type: 'POST'
                },
                columns: [{
                        data: 'ice_name'
                    },
                    {
                        data: 'ice_type'
                    },
                    {
                        data: 'ingredients'
                    },
                    {
                        data: 'price'
                    },
                    {
                        "orderable": false,
                        "data": null,
                        "defaultContent": "<button type=\"button\" class=\"btn btn-warning btn-sm edit-btn\" id=\"edit\">Edit</button> <button type=\"button\" class=\"btn btn-danger btn-sm delete-btn\" id=\"delete\">Delete</button>"
                    }

                ]
            });

            $('#add').click(function() {
                flag = "POST";
                $('#myFormTitle').text("Add Flavor");
                $('#ice_name').val("");
                $('#ice_type').val("");
                $('#ingredients').val("");
                $('#price').val("");
                $('#save').text("Save Flavor");
                $('#feedback').text("");
                $('#myForm').modal('show');

            });

            function postToServer(obj, callBack) {
                $.post("?flag=" + flag,
                    JSON.stringify(obj),
                    function(data, status) {
                        console.log('PHP response:', data);
                        if (data["status"] == 1) {
                            callBack();
                        } else {
                            $("#feedback").text(data["message"]);
                        }
                    }
                );
            }

            // Click on the save button
            $('#save').click(function() {
                var formControl = document.getElementsByClassName("form-control");
                var data = {};
                var isFilled = true;

                for (var i = 0; i < formControl.length; i++) {
                    if (formControl[i].value === "") {
                        isFilled = false;
                        break;
                    }
                    data[formControl[i].id] = formControl[i].value;
                }

                if (isFilled) {
                    postToServer(data, function() {
                        $('#myForm').modal('hide');
                        table.ajax.reload();
                        swal({
                            title: flag === "POST" ? "New flavor added!" : "Flavor edited!",
                            icon: "success",
                            button: "Close",
                        });
                    });
                } else {
                    swal({
                        title: "Please fill all the fields!",
                        icon: "warning",
                        button: "Close",
                    });
                }
            });

            function readFromServer(obj, callBack) {
                $.post("?flag=read",
                    JSON.stringify(obj),
                    function(data, status) {
                        if (data["status"] == 1) {
                            callBack(data["data"]);
                        } else {
                            $("#feedback").text(data["message"]);
                        }
                    }
                );
            }

            // Click on the edit button
            table.on('click', '#edit', function(e) {
                // Get data from the clicked row
                var row = table.row(e.target.closest('tr')).data();
                var ice_name = row[0];
                readFromServer({
                    "ice_name": ice_name
                }, function(data) {
                    if (data !== null) {
                        flag = "PUT";
                        $('#myFormTitle').text("Edit Flavor");
                        $('#ice_name').val(data["ice_name"]);
                        $('#ice_name').prop("disabled", true);
                        $('#ice_type').val(data["ice_type"]);
                        $('#ingredients').val(data["ingredients"]);
                        $('#price').val(data["price"]);
                        $('#save').text("Save Changes");
                        $('#feedback').text("");
                        $('#myForm').modal('show');
                    } else {
                        console.log('No data found for ice_name: ' + ice_name);
                    }
                });
            });

            // Click on the delete button
            // Click on the delete button
            table.on('click', '#delete', function(e) {
                // Get data from the clicked row
                var row = table.row(e.target.closest('tr')).data();
                var ice_name = row[0];
                readFromServer({
                    "ice_name": ice_name
                }, function(data) {
                    if (data !== null) {
                        flag = "DELETE";
                        $('#myFormTitle').text("Delete Flavor");
                        $('#ice_name').val(data["ice_name"]);
                        $('#ice_name').prop("disabled", true);
                        $('#ice_type').val(data["ice_type"]);
                        $('#ingredients').val(data["ingredients"]);
                        $('#price').val(data["price"]);
                        $('#feedback').text("");
                        $('#save').hide();
                        $('#confirm-delete').show();
                        $('#myForm').modal('show');
                    } else {
                        console.log('No data found for ice_name: ' + ice_name);
                    }
                });
            });

            // Click on the confirm delete button
            $('#confirm-delete').click(function() {
                if (flag === "DELETE") {
                    swal({
                            title: "Are you sure?",
                            text: "Once deleted, you will not be able to recover this flavor!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                var formControl = document.getElementsByClassName("form-control");
                                var data = {};
                                for (var i = 0; i < formControl.length; i++) {
                                    data[formControl[i].id] = formControl[i].value;
                                }
                                postToServer(data, function() {
                                    $('#myForm').modal('hide');
                                    table.ajax.reload();
                                    swal("Flavor deleted!", {
                                        icon: "success",
                                    });
                                });
                            } else {
                                swal("Your flavor is safe!");
                            }
                        });
                }
            });

            // Hide the Confirm Delete button when the modal is closed
            $('#myForm').on('hidden.bs.modal', function() {
                $('#confirm-delete').hide();
            });
        </script>
    </body>

    </html>
<?php
}

if (isset($_REQUEST["flag"])) {
    if ($_REQUEST["flag"] == "show") {
        $con = openConnection();

        // Count total rows
        $sqlCount = "SELECT count(*) FROM flavors;";

        // Return data
        $length = intval($_REQUEST["length"]);
        $start = intval($_REQUEST["start"]);
        $sqlData = "SELECT ice_name, ice_type, ingredients, price FROM flavors WHERE ice_name LIKE CONCAT('%', :search, '%') LIMIT $length OFFSET $start";

        $data = array();
        $data["draw"] = intval($_REQUEST["draw"]);
        $data["recordsTotal"] = querySingleValue($con, $sqlCount, array());
        $param = array("search" => $_REQUEST["search"]["value"] . "%");
        $data["data"] = queryArrayRowsValues($con, $sqlData, $param);
        $data["recordsFiltered"] = sizeof($data["data"]);

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($data);
    } else if ($_REQUEST["flag"] == "POST") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);

            // Validate the data
            if (empty($data['ice_name']) || empty($data['ice_type']) || empty($data['ingredients']) || empty($data['price'])) {
                throw new Exception('All fields are required');
            }

            $sql = "INSERT INTO flavors (ice_name, ice_type, ingredients, price) VALUES (:ice_name, :ice_type, :ingredients, :price)";
            createRow($con, $sql, $data);
            $response["status"] = 1;
            $response["message"] = "Data has been successfully added";
            $response["data"] = $data;
        } catch (Exception $e) {
            $response["status"] = 0;
            $response["message"] = $e->getMessage();
            $response["data"] = null;
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($response);
    } else if ($_REQUEST["flag"] == "read") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $param = json_decode($body, true);
            $sql = "SELECT  ice_name, ice_type, ingredients, price FROM flavors WHERE ice_name=:ice_name;";
            $data = queryArrayValue($con, $sql, $param);
            $response["status"] = 1;
            $response["message"] = "Ok";
            $response["data"] = $data;
        } catch (Exception $e) {
            $response["status"] = 0;
            $response["message"] = $e->getMessage();
            $response["data"] = null;
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($response);
        // PHP
    } else if ($_REQUEST["flag"] == "PUT") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $sql = "UPDATE flavors SET  ice_type = :ice_type, ingredients = :ingredients, price = :price WHERE ice_name=:ice_name;";

            updateRow($con, $sql, $data);
            $response["status"] = 1;
            $response["message"] = "Data has been successfully edited";
            $response["data"] = $data;
        } catch (Exception $e) {
            $response["status"] = 0;
            $response["message"] = $e->getMessage();
            $response["data"] = null;
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($response);
    } else if ($_REQUEST["flag"] == "DELETE") {
        $response = array();
        try {
            $con = openConnection();
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);

            // Check if $data is not null and contains 'ice_name'
            if ($data !== null && isset($data['ice_name'])) {
                $sql = "DELETE FROM flavors WHERE ice_name=:ice_name;";
                deleteRow($con, $sql, array("ice_name" => $data['ice_name']));
                $response["status"] = 1;
                $response["message"] = "Data has been deleted successfully";
                $response["data"] = $data;
            } else {
                throw new Exception('Invalid data: ' . $body);
            }
        } catch (Exception $e) {
            $response["status"] = 0;
            $response["message"] = $e->getMessage();
            $response["data"] = null;
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($response);
    }
} else {
    showUI();
}
?>