<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('getOperatorName')) {
    /**
     * Mengambil nama user yang menginput checksheet form (Operator).
     * Kolom utama di database adalah 'name'.
     * Bukan nama approver/role.
     */
    function getOperatorName($row)
    {
        if (empty($row)) {
            return '-';
        }
        if (is_object($row)) {
            if (!empty($row->name) &&$row->name !== '-') {
                return (string)$row->name;
            }
            if (!empty($row->operator) &&$row->operator !== '-') {
                return (string)$row->operator;
            }
            if (!empty($row->nama) &&$row->nama !== '-') {
                return (string)$row->nama;
            }
            if (!empty($row->empid) &&$row->empid !== '-') {
                return (string)$row->empid;
            }
            return '-';
        }
        if (is_array($row)) {
            if (!empty($row['name']) &&$row['name'] !== '-') {
                return (string)$row['name'];
            }
            if (!empty($row['operator']) &&$row['operator'] !== '-') {
                return (string)$row['operator'];
            }
            if (!empty($row['nama']) &&$row['nama'] !== '-') {
                return (string)$row['nama'];
            }
            if (!empty($row['empid']) &&$row['empid'] !== '-') {
                return (string)$row['empid'];
            }
            return '-';
        }
        return '-';
    }
}