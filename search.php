<?php

session_start();

require_once("db.php");

$limit = 4;

$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$start_from = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = ""; // Initialize the $sql variable

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    if ($filter == 'city') {
        $sql = "SELECT * FROM company WHERE city='$search'";
    } else if ($filter == 'searchBar') {
        $sql = "SELECT * FROM job_post WHERE jobtitle LIKE '%$search%' LIMIT $start_from, $limit";
    } else if ($filter == 'experience') {
        $sql = "SELECT * FROM job_post WHERE experience>='$search' LIMIT $start_from, $limit";
    }
}

if (!empty($sql)) {
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($filter == 'city') {
                $companyId = $row['id_company'];
                $companyLogo = $row['logo'];
                $companyName = $row['companyname'];
                $companyCity = $row['city'];

                $sql1 = "SELECT * FROM job_post WHERE id_company='$companyId' LIMIT $start_from, $limit";
                $result1 = $conn->query($sql1);
            } else {
                $companyId = $row['id_company'];
                $sql1 = "SELECT * FROM company WHERE id_company='$companyId'";
                $result1 = $conn->query($sql1);
            }

            if ($result1 && $result1->num_rows > 0) {
                while ($row1 = $result1->fetch_assoc()) {
                    if ($filter != 'city') {
                        $companyLogo = $row1['logo'];
                        $companyName = $row1['companyname'];
                        $companyCity = $row1['city'];
                    }
                    ?>
                    <div class="attachment-block clearfix">
                        <img class="attachment-img" src="uploads/logo/<?php echo $companyLogo; ?>" alt="Attachment Image">
                        <div class="attachment-pushed">
                            <h4 class="attachment-heading">
                                <a href="view-job-post.php?id=<?php echo $row['id_jobpost']; ?>">
                                    <?php echo $row['jobtitle']; ?>
                                </a>
                                <span class="attachment-heading pull-right">$<?php echo $row['maximumsalary']; ?>/Month</span>
                            </h4>
                            <div class="attachment-text">
                                <div><strong><?php echo $companyName; ?> | <?php echo $companyCity; ?> | Experience <?php echo $row['experience']; ?> Years</strong></div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    } else {
        echo "<p>No jobs found.</p>";
    }
} else {
    echo "<p>No search criteria provided.</p>";
}

$conn->close();
?>
