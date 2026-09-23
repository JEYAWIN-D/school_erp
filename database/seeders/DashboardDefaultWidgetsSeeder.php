<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Default dashboard cards have been permanently removed.
 * This seeder is a no-op to ensure no default cards are ever automatically seeded.
 */
class DashboardDefaultWidgetsSeeder extends Seeder
{
    public function run(): void
    {
        // No default cards are seeded. Dashboard starts completely clean with "+ Add Card".
    }
}
