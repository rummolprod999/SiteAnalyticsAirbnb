<?php require_once 'templates/navigation.php' ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div>
                <h1 class="text-center">Availability tables</h1></div>
        </div>
    </div>
</div>
<div id="article">
    <div class="container-fluid">
        <?php foreach ($data['matrix'] as $matrix): ?>
            <?php if (count($matrix) > 0): ?>

                <div class="row border border-2 border-secondary rounded mb-3 p-2">
                    <div style="padding-left: 5px; padding-right: 5px;" class="py-4 col-12">
                        <h2><?php echo $matrix[0][0]['date']->format('F') ?></h2>
                        <div class="table-responsive">
                            <table class="matrix-table table table-bordered">
                                <thead>
                                <tr>
                                    <th style="padding: .75rem 0;">&nbsp;</th>
                                    <?php for ($d = 1, $dMax = count($matrix[0]); $d <= $dMax; $d++) {
                                        echo "<th class='subtable' style='min-width: 41px'>$d</th>";
                                    } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($matrix

                                as $month): ?>
                                <tr>
                                    <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: .75rem 0;">
                                        <a class="table_name_link"
                                           title="<?php echo (string)($month[0]['apartment_name']) ?>"
                                           target="_blank"
                                           href="<?php echo "/stat/{$month[0]['id']}" ?>"><?php echo $month[0]['apartment_name'] ?></a>
                                    </td>
                                    <?php foreach ($month as $day): ?>
                                        <?php
                                        if ($day['available'] === '1' && $day['bookable'] === '1') {
                                            echo "<td style='padding: .75rem 0; width: 30px;' class=\"subtable table-success\"><span class=\"text-dark\">\${$day['price_day']}</span></td>";
                                        } else {
                                            echo "<td style='background-color: #fff;' class='subtable table-secondary'>&nbsp;</td>";
                                        } ?>
                                    <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
