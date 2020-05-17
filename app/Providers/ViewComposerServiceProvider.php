<?php
namespace App\Providers;
use App\Http\ViewComposers\AuthToViewComposer;
use Illuminate\Support\ServiceProvider;
class ViewComposerServiceProvider extends ServiceProvider {
	public function boot() {
		view()->composer('*',AuthToViewComposer::class);
	}
}
