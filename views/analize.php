<?php
/** @var App\Models\User[] $statics */
?>
<?php if ($skipped_rows): ?>
<p>Повреждены или отсутствуют данные в строках:</p>
<ul>
    <?php foreach ($skipped_rows as $row): ?>
    <li><?= $row ?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>
<table>
    <thead>
        <tr>
            <th>Сountry</th>
            <th>Сity</th>
            <th>Is_active</th>
            <th>Gender</th>
            <th>Has_children</th>
            <th>Family_status</th>
            <th>Salary</th>
            <th>Birth_date</th>
            <th>Registration_date</th>
        </tr>
    </thead>
    <tbody>   
        <?php foreach ($statics as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user?->country ?? '') ?></td>
                <td><?= htmlspecialchars($user?->city ?? '') ?></td>
                <td><?= htmlspecialchars($user?->is_active) == 1 ? 'yes' : 'no' ?></td>
                <td><?= htmlspecialchars($user?->gender ?? '') ?></td>
                <td><?= htmlspecialchars($user?->has_children) == 1 ? 'yes' : 'no' ?></td>
                <td><?= htmlspecialchars($user?->family_status ?? '') ?></td>
                <td><?= htmlspecialchars($user?->salary ?? '') ?></td>
                <td><?= htmlspecialchars($user?->birth_date ?? '') ?></td>
                <td><?= htmlspecialchars($user?->registration_date ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
