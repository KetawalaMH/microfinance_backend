<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberType;

class MemberTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['member_type' => 'Staff', 'is_active' => true],
            ['member_type' => 'Individual', 'is_active' => true],
            ['member_type' => 'Cooperative', 'is_active' => true],
        ];

        foreach ($types as $type) {
            MemberType::updateOrCreate(
                ['member_type' => $type['member_type']],
                ['is_active' => $type['is_active']]
            );
        }
    }
}
