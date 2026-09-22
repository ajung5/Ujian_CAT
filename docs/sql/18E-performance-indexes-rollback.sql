ALTER TABLE jawabs
DROP INDEX uq_jawabs_question_package_user,
DROP INDEX idx_jawabs_user_status_soal,
DROP INDEX idx_jawabs_soal_kelas_status_user;

ALTER TABLE countexamtimes
DROP INDEX uq_countexamtimes_soal_user;

ALTER TABLE detailsoals DROP INDEX idx_detailsoals_soal_status;

ALTER TABLE distribusisoals
DROP INDEX uq_distribusisoals_soal_kelas,
DROP INDEX idx_distribusisoals_kelas;