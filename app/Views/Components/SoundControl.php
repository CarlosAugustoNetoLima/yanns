<?php
// Props: state ('muted', 'unmuted')
$state = $state ?? 'unmuted';
?>
<button class="sound-control state-<?= $state?>" aria-label="<?= $state === 'muted' ? 'Unmute' : 'Mute'?>"
    data-state="<?= $state?>" onclick="toggleSound(this)">
    <span class="icon-muted" style="<?= $state === 'muted' ? '' : 'display: none;'?>">
        <?=\App\Helpers\IconHelper::controlSpeakerOff(24, 'var(--color-neutral-0)')?>
    </span>
    <span class="icon-unmuted" style="<?= $state === 'unmuted' ? '' : 'display: none;'?>">
        <?=\App\Helpers\IconHelper::controlSpeaker(24, 'var(--color-neutral-0)')?>
    </span>
</button>