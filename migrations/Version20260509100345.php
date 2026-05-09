<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509100345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, appointment_date DATE NOT NULL, appointment_time TIME NOT NULL, status VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, prescription_path VARCHAR(255) DEFAULT NULL, patient_id INT NOT NULL, doctor_id INT NOT NULL, INDEX IDX_FE38F8446B899279 (patient_id), INDEX IDX_FE38F84487F4FB17 (doctor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE doctor (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(15) NOT NULL, specialization VARCHAR(255) NOT NULL, license_number VARCHAR(50) NOT NULL, experience VARCHAR(50) NOT NULL, hospital VARCHAR(255) NOT NULL, office_place VARCHAR(255) NOT NULL, about VARCHAR(1000) DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8446B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84487F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
        $this->addSql('ALTER TABLE patient CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1ADAD7EBE7927C74 ON patient (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8446B899279');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84487F4FB17');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE doctor');
        $this->addSql('DROP INDEX UNIQ_1ADAD7EBE7927C74 ON patient');
        $this->addSql('ALTER TABLE patient CHANGE email email VARCHAR(255) NOT NULL');
    }
}
