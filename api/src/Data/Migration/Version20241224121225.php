<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241224121225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE auth_user_company (id UUID NOT NULL, company_id UUID NOT NULL, company_city_id UUID NOT NULL, user_id UUID NOT NULL, company_name VARCHAR(255) NOT NULL, company_inn INT NOT NULL, company_ogrn INT NOT NULL, company_city_name VARCHAR(255) NOT NULL, PRIMARY KEY(id, company_id, company_city_id))');
        $this->addSql('CREATE INDEX IDX_E9AA1E41A76ED395 ON auth_user_company (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E9AA1E41EDC0A7A3FBF7A829 ON auth_user_company (company_inn, company_ogrn)');
        $this->addSql('COMMENT ON COLUMN auth_user_company.user_id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('CREATE TABLE auth_users (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, join_confirm_token_value VARCHAR(255) DEFAULT NULL, join_confirm_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8A1F49CE7927C74 ON auth_users (email)');
        $this->addSql('COMMENT ON COLUMN auth_users.id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.join_confirm_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE auth_user_company ADD CONSTRAINT FK_E9AA1E41A76ED395 FOREIGN KEY (user_id) REFERENCES auth_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE auth_user_company DROP CONSTRAINT FK_E9AA1E41A76ED395');
        $this->addSql('DROP TABLE auth_user_company');
        $this->addSql('DROP TABLE auth_users');
    }
}
