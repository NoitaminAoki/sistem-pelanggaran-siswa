<?php

namespace App\Helpers;

class MenuHelper
{
    static $MENU = [
        [
            'title' => 'Dashboard',
            'is_admin_menu' => false,
            'sub_title' => 'Home',
            'name' => 'admin_dashboard',
            'root' => '',
            'route_name' => 'admin.dashboard',
            'route_validate' => ['admin.dashboard', 'admin.dashboard.*'],
            'icon_class' => 'fas fa-tachometer-alt',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => false],
            ],
            'branch' => [],
        ],
        [
            'title' => 'Catatan Siswa',
            'is_admin_menu' => false,
            'sub_title' => '',
            'name' => 'student_record',
            'root' => '',
            'route_name' => '=',
            'route_validate' => ['record.student', 'record.student.*'],
            'icon_class' => 'fas fa-book',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
            ],
            'branch' => [
                ['name' => 'student_violation', 'is_admin_menu' => false, 'title' => 'Pelanggaran', 'route_name' => 'record.student.violation', 'route_validate' => ['record.student.violation', 'record.student.violation.*']],
                ['name' => 'student_sanction', 'is_admin_menu' => true,  'title' => 'Sanksi', 'route_name' => 'record.student.sanction', 'route_validate' => ['record.student.sanction', 'record.student.sanction.*']],
                ['name' => 'student_achievement', 'is_admin_menu' => false, 'title' => 'Prestasi', 'route_name' => 'record.student.achievement', 'route_validate' => ['record.student.achievement', 'record.student.achievement.*']],
            ],
        ],
        [
            'title' => 'Guru',
            'is_admin_menu' => true,
            'sub_title' => '',
            'name' => 'master_teacher',
            'root' => 'master',
            'route_name' => 'master.teacher',
            'route_validate' => ['master.teacher', 'master.teacher.*'],
            'icon_class' => 'fas fa-chalkboard-teacher',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'master', 'has_link' => false],
                ['name' => 'teacher', 'has_link' => false],
            ],
            'branch' => [],
        ],
        [
            'title' => 'Siswa',
            'is_admin_menu' => true,
            'sub_title' => '',
            'name' => 'master_student',
            'root' => 'master',
            'route_name' => 'master.student',
            'route_validate' => ['master.student', 'master.student.*'],
            'icon_class' => 'fas fa-users',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'master', 'has_link' => false],
                ['name' => 'student', 'has_link' => false],
            ],
            'branch' => [],
        ],
        [
            'title' => 'Hukum',
            'is_admin_menu' => true,
            'sub_title' => '',
            'name' => 'master_law',
            'root' => 'master',
            'route_name' => '=',
            'route_validate' => ['master.law', 'master.law.*'],
            'icon_class' => 'fas fa-balance-scale',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => false],
            ],
            'branch' => [
                ['name' => 'master_violation', 'is_admin_menu' => false, 'title' => 'Pelanggaran', 'route_name' => 'master.law.violation', 'route_validate' => ['master.law.violation', 'master.law.violation.*']],
                ['name' => 'master_sanction', 'is_admin_menu' => false, 'title' => 'Sanksi', 'route_name' => 'master.law.sanction', 'route_validate' => ['master.law.sanction', 'master.law.sanction.*']],
            ],
        ],
        [
            'title' => 'Prestasi',
            'is_admin_menu' => true,
            'sub_title' => '',
            'name' => 'master_achievement',
            'root' => 'master',
            'route_name' => 'master.achievement',
            'route_validate' => ['master.achievement', 'master.achievement.*'],
            'icon_class' => 'fas fa-trophy',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'master', 'has_link' => false],
                ['name' => 'achievement', 'has_link' => false],
            ],
            'branch' => [],
        ],
        [
            'title' => 'Pelanggaran',
            'is_admin_menu' => false,
            'sub_title' => '',
            'name' => 'report_violation',
            'root' => 'report',
            'route_name' => '=',
            'route_validate' => ['report.violation', 'report.violation.*'],
            'icon_class' => 'fas fa-file-alt',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'report', 'has_link' => false],
                ['name' => 'violation', 'has_link' => false],
            ],
            'branch' => [
                ['name' => 'report_violation_all', 'is_admin_menu' => false, 'title' => 'Keseluruhan', 'route_name' => 'report.violation', 'route_validate' => ['report.violation']],
                ['name' => 'report_violation_summary', 'is_admin_menu' => false, 'title' => 'Summary', 'route_name' => 'report.violation.summary', 'route_validate' => ['report.violation.summary']],
            ],
        ],
        [
            'title' => 'Sanksi',
            'is_admin_menu' => false,
            'sub_title' => '',
            'name' => 'report_sanction',
            'root' => 'report',
            'route_name' => '=',
            'route_validate' => ['report.sanction', 'report.sanction.*'],
            'icon_class' => 'fas fa-file-alt',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'report', 'has_link' => false],
                ['name' => 'sanction', 'has_link' => false],
            ],
            'branch' => [
                ['name' => 'report_sanction_all', 'is_admin_menu' => false, 'title' => 'Keseluruhan', 'route_name' => 'report.sanction.all', 'route_validate' => ['report.sanction.all']],
                ['name' => 'report_sanction_summary', 'is_admin_menu' => false, 'title' => 'Summary', 'route_name' => 'report.sanction.summary', 'route_validate' => ['report.sanction.summary']],
            ],
        ],
        [
            'title' => 'Prestasi',
            'is_admin_menu' => false,
            'sub_title' => '',
            'name' => 'report_achievement',
            'root' => 'report',
            'route_name' => '=',
            'route_validate' => ['report.achievement', 'report.achievement.*'],
            'icon_class' => 'fas fa-file-alt',
            'breadcrumb' => [
                ['name' => 'home', 'has_link' => true, 'route_name' => 'admin.dashboard'],
                ['name' => 'report', 'has_link' => false],
                ['name' => 'achievement', 'has_link' => false],
            ],
            'branch' => [
                ['name' => 'report_achievement_all', 'is_admin_menu' => false, 'title' => 'Keseluruhan', 'route_name' => 'report.achievement.all', 'route_validate' => ['report.achievement.all']],
                ['name' => 'report_achievement_summary', 'is_admin_menu' => false, 'title' => 'Summary', 'route_name' => 'report.achievement.summary', 'route_validate' => ['report.achievement.summary']],
            ],
        ],
        [
            'title' => 'Peringkat',
            'is_admin_menu' => false,
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
                ['name' => 'report_rank_best', 'is_admin_menu' => false, 'title' => 'Siswa Terbaik', 'route_name' => 'report.rank.best', 'route_validate' => ['report.rank.best']],
                ['name' => 'report_rank_bad', 'is_admin_menu' => false, 'title' => 'Siswa Terburuk', 'route_name' => 'report.rank.bad', 'route_validate' => ['report.rank.bad']],
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
