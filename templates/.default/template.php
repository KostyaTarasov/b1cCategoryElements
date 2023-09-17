<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>

<?php if (!empty($arResult["SECTIONS"])): ?>
    <ul>
        <?php foreach ($arResult["SECTIONS"] as $section): ?>
            <li>
                <strong><?= $section["NAME"] ?></strong>
                <ul>
                    <?php foreach ($section["ELEMENTS"] as $element): ?>
                        <li>
                            <?= $element["NAME"] ?> (<?= implode(", ", $element["TAGS"]) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
