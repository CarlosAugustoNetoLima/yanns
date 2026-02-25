<div class="input-wrapper <?= $mode === 'Dark' ? 'input-dark' : ''?> <?= $class ?? ''?>"
    data-status="<?= strtolower($status ?? 'default')?>">
    <?php if ($label ?? true): ?>
    <div class="input-label-container">
        <label class="input-label">
            <?= $labelText ?? 'Label'?>
        </label>
        <?php if ($optional ?? false): ?>
        <span class="input-optional">(optional)</span>
        <?php
    endif; ?>
        <?php if ($required ?? false): ?>
        <span class="input-required">*</span>
        <?php
    endif; ?>
        <?php if ($info ?? false): ?>
        <div class="input-info-icon" tabindex="0">
            <?=\App\Helpers\IconHelper::informationSlabCircleOutline(14, 'var(--color-info-500-main)')?>
            <div class="input-tooltip">
                <div class="input-tooltip-content">
                    Information
                </div>
                <div class="input-tooltip-arrow"></div>
            </div>
        </div>
        <?php
    endif; ?>
    </div>
    <?php
endif; ?>

    <div class="input-group">
        <?php if (($type ?? 'Input') === 'Select-Button'): ?>
        <button type="button" class="input-select-button" <?=isset($disabled) && $disabled ? 'disabled' : ''?>>
            <span class="input-select-text">POR +351</span>
            <span class="input-select-icon">
                <?=\App\Helpers\IconHelper::chevronDown(14, 'currentColor')?>
            </span>
        </button>
        <?php
endif; ?>

        <div
            class="input-container input-<?= strtolower($size ?? 'Large')?> <?= isset($error) ? 'input-error' : ''?> <?= isset($disabled) && $disabled ? 'input-disabled' : ''?>">
            <?php if ($iconLeft ?? false): ?>
            <span class="input-icon-left">
                <?=\App\Helpers\IconHelper::emailOutline(isset($size) && $size === 'Large' ? 16 : 14, 'currentColor')?>
            </span>
            <?php
endif; ?>

            <input type="<?= $inputType ?? 'text'?>" class="input-field"
                placeholder="<?= $placeholder ?? 'Placeholder'?>" value="<?= $value ?? ''?>" <?=isset($disabled) &&
                $disabled ? 'disabled' : ''?>
            >

            <?php if ($iconRight ?? false): ?>
            <span class="input-icon-right">
                <?=\App\Helpers\IconHelper::chevronDown(isset($size) && $size === 'Large' ? 16 : 14, 'currentColor')?>
            </span>
            <?php
endif; ?>
        </div>
    </div>

    <?php if (isset($error) || isset($helperText)): ?>
    <div class="input-helper-container">
        <span class="input-helper-text <?= isset($error) ? 'is-error' : ''?>">
            <?= $error ?? ($helperText ?? 'Helper text')?>
        </span>
    </div>
    <?php
endif; ?>
</div>