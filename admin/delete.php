<td>
    <!-- Edit button -->
    <a href="edit_user.php?id=<?= (int)$user['id'] ?>"
        class="btn"
        style="background:#4CAF50; color:white; padding:5px 10px; text-decoration:none; border-radius:4px;">
        Edit
    </a>

    <!-- Delete button (only if not self) -->
    <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
        <a href="delete_user.php?id=<?= (int)$user['id'] ?>"
            onclick="return confirm('Are you sure you want to delete this user?');"
            class="btn"
            style="background:#f44336; color:white; padding:5px 10px; text-decoration:none; border-radius:4px;">
            Delete
        </a>
    <?php endif; ?>

    <!-- Change role -->
    <form method="POST" action="users.php" style="display:inline-block; margin-left:5px;">
        <input type="hidden" name="action" value="change_role">
        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
        <select name="role" onchange="this.form.submit()">
            <option value="customer" <?= $user['role'] === 'customer' ? ' selected' : '' ?>>Customer</option>
            <option value="admin" <?= $user['role'] === 'admin' ? ' selected' : '' ?>>Admin</option>
        </select>
    </form>

    <!-- Activate / Deactivate -->
    <form method="POST" action="users.php" style="display:inline-block; margin-left:5px;">
        <input type="hidden" name="action" value="toggle_status">
        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
        <button class="btn <?= ($user['status'] ?? 'active') === 'active' ? 'delete' : 'add' ?>" type="submit">
            <?= ($user['status'] ?? 'active') === 'active' ? 'Deactivate' : 'Activate' ?>
        </button>
    </form>
</td>