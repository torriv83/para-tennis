<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public array $languages = [
        'no' => ['name' => 'Norsk', 'flag' => '🇳🇴'],
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'uk' => ['name' => 'Українська', 'flag' => '🇺🇦'],
    ];

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function setLocale(string $locale): void
    {
        if (! in_array($locale, SetLocale::SUPPORTED_LOCALES)) {
            return;
        }

        session()->put('locale', $locale);
        App::setLocale($locale);
        $this->currentLocale = $locale;

        $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
