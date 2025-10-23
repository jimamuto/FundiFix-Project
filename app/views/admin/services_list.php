<?php

require_once _DIR_ . '/../layouts/header.php';

?>

<div class="container py-5">
    <h2 class="mb-4 text-center">All Users</h2>

    <?php if (!empty($users) && is_array($users)): ?>
        <table class="table table-striped table-bordered">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td class="text-center">
                            <?= $user['is_verified'] ? 
                                '<span class="badge bg-success">Verified</span>' : 
                                '<span class="badge bg-danger">Not Verified</span>' ?>
                        </td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="text-center">
                            <a href="?action=deleteUser&id=<?= urlencode($user['id']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this user?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">
            No users found in the database.
        </div>
    <?php endif; ?>
</div>

<?php

require_once _DIR_ . '/../layouts/footer.php';


?>