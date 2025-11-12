<?php include '../layouts/header.php'; ?>

<div class="container">
    <h2>Manage Fundis</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Skills</th>
                <th>Location</th>
                <th>Phone</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fundis as $fundi): ?>
            <tr>
                <td><?= htmlspecialchars($fundi['name']) ?></td>
                <td><?= htmlspecialchars($fundi['email']) ?></td>
                <td><?= htmlspecialchars($fundi['skills']) ?></td>
                <td><?= htmlspecialchars($fundi['location']) ?></td>
                <td><?= htmlspecialchars($fundi['phone_number']) ?></td>
                <td><?= date('M j, Y', strtotime($fundi['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../layouts/footer.php'; ?>
