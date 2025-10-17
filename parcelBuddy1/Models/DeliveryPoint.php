<?php

class DeliveryPoint
{
    function getAdminsParcelsList()
    {
        include 'connection.php';
        // read request
        $parcel_id = $_GET['parcel_id'] ?? '';
        $postal_code = $_GET['postcode'] ?? '';
        $name = $_GET['name'] ?? '';
        $address_1 = $_GET['address_1'] ?? '';
        $address_2 = $_GET['address_2'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        global $connection;

        // query to get all records and filter
        $stmt = $connection->prepare("SELECT delivery_point.*, delivery_status.status_text 
        FROM delivery_point
        JOIN delivery_status ON delivery_point.status = delivery_status.id
        WHERE (
            delivery_point.id LIKE :parcel_id 
            AND delivery_point.postcode LIKE :postal_code 
            AND delivery_point.name LIKE :name 
            AND delivery_point.address_1 LIKE :address_1 
            AND delivery_point.address_2 LIKE :address_2
        ) 
        ORDER BY $sort 
       ");

        // bind params and execute
        $stmt->bindValue(':parcel_id', '%' . $parcel_id . '%');
        $stmt->bindValue(':postal_code', '%' . $postal_code . '%');
        $stmt->bindValue(':name', '%' . $name . '%');
        $stmt->bindValue(':address_1', '%' . $address_1 . '%');
        $stmt->bindValue(':address_2', '%' . $address_2 . '%');

        $stmt->execute();

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($res as $row) {
            // get deliverer data
            $stmt2 = $connection->prepare("SELECT * FROM delivery_users WHERE userid = :deliverer");
            $stmt2->bindValue(':deliverer', $row['deliverer']);
            $stmt2->execute();

            $deliverer = $stmt2->fetch(); ?>

            <tr>
                <td><?php echo ($row['id']) ?></td>
                <td><?php echo ($row['name']) ?></td>
                <td><?php echo ($row['address_1']) ?></td>
                <td><?php echo ($row['address_2']) ?></td>
                <td><?php echo ($row['postcode']) ?></td>

                <td><?php echo ($row['lat']) ?></td>
                <td><?php echo ($row['lng']) ?></td>
                <td><?php echo ($deliverer['username']) ?></td>
                <td class="text-center"><?php echo $row['del_photo'] == '' ? '' : '' ?>
                    <?php
                    if ($row['del_photo'] != '') {
                    ?>
                        <a href="images/<?php echo ($row['del_photo']) ?>" target="_blank">
                            <img src="images/<?php echo ($row['del_photo']) ?>" height="40" width="40" alt="[image]">
                            <br>
                            Show
                        </a>
                    <?php
                    }
                    ?>
                </td>
                <td><?php echo ($row['status_text']) ?></td>

                <td>
                    <a class="btn btn-info btn-sm" href="UpdateController.php?id=<?php echo ($row['id']) ?>">Update</a>&nbsp;&nbsp;
                </td>
                <td>
                    <a class="btn btn-warning btn-sm" href="?delete=true&id=<?php echo ($row['id']) ?>">Delete</a>
                </td>
            </tr>
        <?php
        }
    }



    public function update()
    {
        include 'connection.php';
        if (isset($_POST['update'])) {
            //read values
            $id = $_POST['id'];
            $name = $_POST['name'];
            $address_1 = $_POST['address_1'];
            $address_2 = $_POST['address_2'];
            $lat = $_POST['lat'];
            $lng = $_POST['lng'];
            $postcode = $_POST['postcode'];
            $status = $_POST['status'];
            $deliverer = $_POST['deliverer'];

            //chnage files
            if (isset($_FILES['image'])) {
                $uploadDirectory = 'images/';
                $filename = $_FILES['image']['name'];
                $targetPath =  $uploadDirectory . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $connection->query("UPDATE delivery_point SET delivery_point.del_photo='$filename'
                  WHERE delivery_point.id = $id");
                    return true;
                }
            }

            //set data
            $stmt = $connection->prepare("UPDATE delivery_point 
                SET 
                    `name` = :name,
                    address_1 = :address_1,
                    address_2 = :address_2,
                    lat = :lat,
                    lng = :lng,
                    deliverer = :deliverer,
                    postcode = :postcode,
                    status = :status
                WHERE 
                    id = :id
            ");
            //bond and execute
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address_1', $address_1);
            $stmt->bindParam(':address_2', $address_2);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->bindParam(':deliverer', $deliverer);
            $stmt->bindParam(':postcode', $postcode);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            return $result;
        }
        return false;
    }

    function store()
    {
        include 'connection.php';
        $name = $_POST['name'];
        $address_1 = $_POST['address_1'];
        $address_2 = $_POST['address_2'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $postcode = $_POST['postcode'];
        $status = $_POST['status'];
        $deliverer = $_POST['deliverer'];
        $uploadedFileName = '';

        // upload files
        if (isset($_FILES['image'])) {
            $uploadDirectory = 'images/';
            $uploadedFileName = $_FILES['image']['name'];
            $targetPath =  $uploadDirectory . $uploadedFileName;
            move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        }
        global  $connection;

        // prepare
        $stmt = $connection->prepare("INSERT INTO `delivery_point` 
            (`name`, `address_1`, `address_2`, `postcode`, `deliverer`, `lat`, `lng`, `status`, `del_photo`) 
            VALUES
            (:nm, :address_1, :address_2, :postcode, :deliverer, :lat, :lng, :stt, :del_photo)
        ");

        $stmt->bindParam(':nm', $name);
        $stmt->bindParam(':address_1', $address_1);
        $stmt->bindParam(':address_2', $address_2);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':deliverer', $deliverer);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':stt', $status);
        $stmt->bindParam(':del_photo', $uploadedFileName);

        // run query
        $result = $stmt->execute();

        return $result;
    }

    function delete()
    {
        include 'connection.php';

        if (isset($_GET['delete'])) {
            //delete record
            $id = $_GET['id'];
            global $connection;
            $obj = $connection->prepare("DELETE FROM delivery_point WHERE delivery_point.id = :id");
            $obj->bindParam(':id', $id);
            try {
                $obj->execute();
            } catch (PDOException $e) {
                echo "Something Went Wrong.";
            }
        }
    }


    function getDelivererParcelsList()
    {
        include 'connection.php';
        $userid = $_SESSION['user_id'];

        //filters
        $parcel_id = isset($_GET['parcel_id']) ? $_GET['parcel_id'] : '';
        $postal_code = isset($_GET['postcode']) ? $_GET['postcode'] : '';
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        $address1 = isset($_GET['address_1']) ? $_GET['address_1'] : '';
        $address2 = isset($_GET['address_2']) ? $_GET['address_2'] : '';
        $sort = $_GET['sort'] ?? 'id';

        global $connection;
        // get data
        $query = "SELECT delivery_point.*, delivery_status.status_text 
            FROM delivery_point
            JOIN delivery_status ON delivery_point.status = delivery_status.id
            WHERE 
                delivery_point.deliverer = :userid
                AND (
                    delivery_point.id LIKE :parcel_id 
                    AND delivery_point.postcode LIKE :postcode 
                    AND delivery_point.name LIKE :nm 
                    AND delivery_point.address_1 LIKE :address_1 
                    AND delivery_point.address_2 LIKE :address_2
                )
                ORDER BY $sort 
           ";

        //prepare
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindValue(':parcel_id', '%' . $parcel_id . '%');
        $stmt->bindValue(':postcode', '%' . $postal_code . '%');
        $stmt->bindValue(':nm', '%' . $name . '%');
        $stmt->bindValue(':address_1', '%' . $address1 . '%');
        $stmt->bindValue(':address_2', '%' . $address2 . '%');


        $stmt->execute();

        if ($stmt->errorCode() !== '00000') {
            $errorInfo = $stmt->errorInfo();
            echo "SQL error: {$errorInfo[2]}";
        }


        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($res as $row) {
            ///write in table
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['address_1'] ?></td>
                <td><?= $row['address_2'] ?></td>
                <td><?= $row['postcode'] ?></td>
                <td><?= $row['lat'] ?></td>
                <td><?= $row['lng'] ?></td>
                <td class="text-center"><?= $row['del_photo'] == '' ? '' : '' ?>
                    <?php
                    if ($row['del_photo'] != '') {
                    ?>
                        <a href="images/<?= $row['del_photo'] ?>" target="_blank"> <img src="images/<?= $row['del_photo'] ?>" height="45" width="45" alt="image">
                            <br>
                            View</a>
                    <?php
                    }
                    ?>
                </td>
                <td><?= $row['status_text'] ?></td>

            </tr>
<?php
        }
    }

    function get($id)
    {
        include 'connection.php';
        //get parcel by id
        global  $connection;
        $result = $connection->query("SELECT * FROM delivery_point WHERE delivery_point.id = '$id'");
        return $result->fetch();
    }
}
