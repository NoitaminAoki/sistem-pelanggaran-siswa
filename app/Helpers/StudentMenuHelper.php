<?php

namespace App\Helpers;

class StudentMenuHelper
{
    static $MENU = [
        [
            'title' => 'Dashboard',
            'sub_title' => 'Home',
            'name' => 'dashboard',
            'root' => '',
            'route_name' => 'student.dashboard',
            'route_validate' => ['student.dashboard', 'student.dashboard.*'],
            'icon_class' => 'fas fa-tachometer-alt',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => false],
            ],
            'branch' => [],
        ],
        [
            'title' => 'Catatan Siswa',
            'sub_title' => '',
            'name' => 'student_record',
            'root' => '',
            'route_name' => '=',
            'route_validate' => ['student.record', 'student.record.*'],
            'icon_class' => 'fas fa-book',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'student.dashboard'],
            ],
            'branch' => [
                ['name' => 'student_violation', 'title' => 'Pelanggaran', 'route_name' => 'student.record.violation', 'route_validate' => ['student.record.violation', 'student.record.violation.*']],
                ['name' => 'student_sanction', 'title' => 'Sanksi', 'route_name' => 'student.record.sanction', 'route_validate' => ['student.record.sanction', 'student.record.sanction.*']],
                ['name' => 'student_achievement', 'title' => 'Prestasi', 'route_name' => 'student.record.achievement', 'route_validate' => ['student.record.achievement', 'student.record.achievement.*']],
            ],
        ],
        [
            'title' => 'Peringkat',
            'sub_title' => '',
            'name' => 'report_rank',
            'root' => 'report',
            'route_name' => '=',
            'route_validate' => ['report.rank', 'report.rank.*'],
            'icon_class' => 'far fa-chart-bar',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'report', 'has_link' => false],
                ['name' => 'rank', 'has_link' => false],
            ],
            'branch' => [
                ['name' => 'report_rank_best', 'title' => 'Siswa Terbaik', 'route_name' => 'report.rank.best', 'route_validate' => ['report.rank.best']],
                ['name' => 'report_rank_bad', 'title' => 'Siswa Terburuk', 'route_name' => 'report.rank.bad', 'route_validate' => ['report.rank.bad']],
            ],
        ],
    ];

    public static function GetMenu()
    {
        return json_decode(json_encode(self::$MENU));
    }

    public static function getMenuByName($name)
    {
        $menu = collect(self::$MENU);
        return (object) $menu->firstWhere('name', $name);
    }
}
