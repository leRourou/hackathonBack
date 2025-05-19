<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519145008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE appointment (id VARCHAR(36) NOT NULL, vehicule_id VARCHAR(36) NOT NULL, garage_id VARCHAR(36) NOT NULL, date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', status VARCHAR(255) NOT NULL, notes LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_FE38F8444A4A3511 (vehicule_id), INDEX IDX_FE38F844C4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE appointment_operation (appointment_id VARCHAR(36) NOT NULL, operation_id VARCHAR(36) NOT NULL, INDEX IDX_8C0BC864E5B533F9 (appointment_id), INDEX IDX_8C0BC86444AC3583 (operation_id), PRIMARY KEY(appointment_id, operation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE garage (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE operation (id VARCHAR(36) NOT NULL, category_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, additionnal_help LONGTEXT DEFAULT NULL, additionnal_comment LONGTEXT DEFAULT NULL, time_unit INT NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_1981A66D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE operation_category (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id VARCHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, phone VARCHAR(10) NOT NULL, is_driver TINYINT(1) NOT NULL, driver_last_name VARCHAR(100) DEFAULT NULL, driver_first_name VARCHAR(100) DEFAULT NULL, driver_phone VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vehicule (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, license_plate VARCHAR(7) NOT NULL, vin VARCHAR(17) NOT NULL, registration_date DATE NOT NULL, mileage INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_292FFF1DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment_operation ADD CONSTRAINT FK_8C0BC864E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment_operation ADD CONSTRAINT FK_8C0BC86444AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operation ADD CONSTRAINT FK_1981A66D12469DE2 FOREIGN KEY (category_id) REFERENCES operation_category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8444A4A3511
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844C4FFF555
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment_operation DROP FOREIGN KEY FK_8C0BC864E5B533F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment_operation DROP FOREIGN KEY FK_8C0BC86444AC3583
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE appointment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE appointment_operation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE garage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE operation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE operation_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vehicule
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
