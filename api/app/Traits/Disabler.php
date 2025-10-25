<?php

namespace App\Traits;

trait Disabler {
    public function getDisabledAtColumn(): string {
        return "disabled_at";
    }

    public function isDisabled(): bool {
        return $this->disabled_at !== null;
    }

    public function isNotDisabled(): bool {
        return !$this->isDisabled();
    }

    public function disable() {
        $this->disabled_at = now();
        return $this->save();
    }

    public function enable() {
        $this->disabled_at = null;
        return $this->save();
    }

    public function toggle() {
        if ($this->isDisabled()) {
            return $this->enable();
        } else {
            return $this->disable();
        }
    }
}
