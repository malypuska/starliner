<?php
$trainDesc = $data['train_description'] ?? null;
$route = $data['route'] ?? null;
?>

<div class="card border-success shadow-sm">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">Маршрут найден!</h4>
    </div>
    <div class="card-body">
        <?php if ($trainDesc): ?>
            <h5>Поезд №: <strong><?= htmlspecialchars($trainDesc['number'] ?? '') ?></strong> <?= htmlspecialchars($trainDesc['name'] ?? '') ?></h5>
        <?php endif; ?>

        <?php if ($route): ?>
            <h6 class="mt-3 text-uppercase text-secondary"><?= htmlspecialchars($route['name'] ?? 'Основной маршрут') ?></h6>
            <p class="text-muted">Направление от <?= htmlspecialchars($route['from'] ?? '') ?> до <?= htmlspecialchars($route['to'] ?? '') ?></p>
            <div class="table-responsive">
                <table class="table table-striped table-hover mt-2">
                    <thead class="table-dark">
                        <tr>
                            <th>Станция</th>
                            <th>Прибытие</th>
                            <th>Отправление</th>
                            <th>Стоянка (мин)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach ($route['stops'] as $stopItem): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($stopItem['station'] ?? '') ?></strong></td>
                                <td><?= !empty($stopItem['arrival_time']) ? htmlspecialchars($stopItem['arrival_time']) : '-' ?></td>
                                <td><?= !empty($stopItem['departure_time']) ? htmlspecialchars($stopItem['departure_time']) : '-' ?></td>
                                <td><?= htmlspecialchars($stopItem['stop_time'] ?? '0') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-warning">Данные о станциях следования отсутствуют.</p>
        <?php endif; ?>
    </div>
</div>