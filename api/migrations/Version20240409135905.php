<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240409135905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `user_project`, `user_project_user` tables and its relationships';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `user_project` (
                id CHAR(36) NOT NULL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(100),
                link TEXT,
                owner_id CHAR(36) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                
                INDEX IDX_user_project_name (name),
                INDEX IDX_user_project_owner_id (owner_id),
                CONSTRAINT FK_user_project_owner_id FOREIGN KEY (owner_id) REFERENCES `user` (id) ON UPDATE CASCADE ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );

        $this->addSql('CREATE TABLE `user_project_user` (
                user_id CHAR(36) NOT NULL,
                project_id CHAR(36) NOT NULL,
                
                UNIQUE U_user_id_project_id (user_id, project_id),
                CONSTRAINT FK_user_project_user_user_id FOREIGN KEY (user_id) REFERENCES `user` (id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT FK_user_project_user_project_id FOREIGN KEY (project_id) REFERENCES `user_project` (id) ON UPDATE CASCADE ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user_project_user`');
        $this->addSql('DROP TABLE `user_project`');
    }
}