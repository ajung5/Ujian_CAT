/*
|--------------------------------------------------------------------------
| Ujian CAT - Performance Indexes
|--------------------------------------------------------------------------
|
| Laravel 13 modernization
| Legacy database: ujian
|
| IMPORTANT:
| - Backup database before running.
| - Designed for the legacy schema.
| - Do not run blindly on databases with different indexes.
| - Duplicate logical keys must be audited first.
|
*/

/*
|--------------------------------------------------------------------------
| jawabs
|--------------------------------------------------------------------------
*/

ALTER TABLE jawabs
ADD UNIQUE INDEX uq_jawabs_question_package_user (no_soal_id, id_soal, id_user),
ADD INDEX idx_jawabs_user_status_soal (id_user, status, id_soal),
ADD INDEX idx_jawabs_soal_kelas_status_user (
    id_soal,
    id_kelas,
    status,
    id_user
);

/*
|--------------------------------------------------------------------------
| countexamtimes
|--------------------------------------------------------------------------
*/

ALTER TABLE countexamtimes
ADD UNIQUE INDEX uq_countexamtimes_soal_user (id_soal, id_user);

/*
|--------------------------------------------------------------------------
| detailsoals
|--------------------------------------------------------------------------
*/

ALTER TABLE detailsoals
ADD INDEX idx_detailsoals_soal_status (id_soal, status);

/*
|--------------------------------------------------------------------------
| distribusisoals
|--------------------------------------------------------------------------
*/

ALTER TABLE distribusisoals
ADD UNIQUE INDEX uq_distribusisoals_soal_kelas (id_soal, id_kelas),
ADD INDEX idx_distribusisoals_kelas (id_kelas);

/*
|--------------------------------------------------------------------------
| Refresh statistics
|--------------------------------------------------------------------------
*/

ANALYZE TABLE jawabs;

ANALYZE TABLE countexamtimes;

ANALYZE TABLE detailsoals;

ANALYZE TABLE distribusisoals;