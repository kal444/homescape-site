CREATE TABLE layout (
  layout_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(20) NOT NULL,
  layout_type VARCHAR(10) NOT NULL,
  PRIMARY KEY(layout_id)
);

CREATE TABLE keyword (
  keyword VARCHAR(25) NOT NULL,
  description VARCHAR(255) NULL,
  word_type VARCHAR(1) NULL,
  PRIMARY KEY(keyword)
);

CREATE TABLE link (
  link_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  link_url VARCHAR(255) NULL,
  description VARCHAR(255) NULL,
  link_type VARCHAR(25) NULL,
  PRIMARY KEY(link_id)
);

CREATE TABLE message (
  message_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(30) NOT NULL,
  subject VARCHAR(50) NOT NULL,
  email_address VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  message_ts DATETIME NULL,
  PRIMARY KEY(message_id)
);

CREATE TABLE media (
  media_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  media_loc VARCHAR(255) NOT NULL,
  media_thumbnail_loc VARCHAR(255) NOT NULL,
  media_intermediate_loc VARCHAR(255) NOT NULL,
  media_type VARCHAR(255) NOT NULL,
  PRIMARY KEY(media_id)
);

CREATE TABLE contact (
  contact_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NULL,
  street VARCHAR(255) NULL,
  city VARCHAR(25) NULL,
  state VARCHAR(2) NULL,
  zip VARCHAR(6) NULL,
  phone VARCHAR(10) NULL,
  PRIMARY KEY(contact_id)
);

CREATE TABLE application_default (
  name VARCHAR(20) NOT NULL,
  value VARCHAR(50) NOT NULL
);

CREATE TABLE page (
  page_name VARCHAR(25) NOT NULL,
  layout_id INTEGER UNSIGNED NOT NULL,
  banner_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(page_name),
  INDEX pages_FKIndex2(banner_id),
  INDEX pages_FKIndex3(layout_id),
  FOREIGN KEY(banner_id)
    REFERENCES media(media_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(layout_id)
    REFERENCES layout(layout_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE item (
  item_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  layout_id INTEGER UNSIGNED NOT NULL,
  media_id INTEGER UNSIGNED NULL,
  caption VARCHAR(50) NULL,
  description TEXT NULL,
  item_type VARCHAR(1) NULL,
  PRIMARY KEY(item_id),
  INDEX items_FKIndex1(media_id),
  INDEX items_FKIndex2(layout_id),
  FOREIGN KEY(media_id)
    REFERENCES media(media_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(layout_id)
    REFERENCES layout(layout_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE media_keyword (
  media_id INTEGER UNSIGNED NOT NULL,
  keyword VARCHAR(25) NOT NULL,
  PRIMARY KEY(media_id, keyword),
  INDEX media_keywords_FKIndex1(media_id),
  INDEX media_keywords_FKIndex2(keyword),
  FOREIGN KEY(media_id)
    REFERENCES media(media_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(keyword)
    REFERENCES keyword(keyword)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE project (
  project_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  contact_id INTEGER UNSIGNED NOT NULL,
  project_image INTEGER UNSIGNED NOT NULL,
  layout_id INTEGER UNSIGNED NOT NULL,
  project_name VARCHAR(25) NOT NULL,
  category VARCHAR(10) NOT NULL,
  title VARCHAR(50) NOT NULL,
  description VARCHAR(255) NOT NULL,
  project_ts DATETIME NOT NULL,
  reference ENUM('Y','N') NULL DEFAULT 'N',
  PRIMARY KEY(project_id),
  INDEX projects_FKIndex1(layout_id),
  INDEX projects_FKIndex2(project_image),
  INDEX project_FKIndex3(contact_id),
  FOREIGN KEY(layout_id)
    REFERENCES layout(layout_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(project_image)
    REFERENCES media(media_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(contact_id)
    REFERENCES contact(contact_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE project_keyword (
  project_id INTEGER UNSIGNED NOT NULL,
  keyword VARCHAR(25) NOT NULL,
  PRIMARY KEY(project_id, keyword),
  INDEX project_keywords_FKIndex1(project_id),
  INDEX project_keywords_FKIndex2(keyword),
  FOREIGN KEY(project_id)
    REFERENCES project(project_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(keyword)
    REFERENCES keyword(keyword)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE project_content (
  project_id INTEGER UNSIGNED NOT NULL,
  item_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(project_id, item_id),
  INDEX project_content_FKIndex1(project_id),
  INDEX project_content_FKIndex2(item_id),
  FOREIGN KEY(project_id)
    REFERENCES project(project_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(item_id)
    REFERENCES item(item_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE item_keyword (
  item_id INTEGER UNSIGNED NOT NULL,
  keyword VARCHAR(25) NOT NULL,
  PRIMARY KEY(item_id, keyword),
  INDEX item_keywords_FKIndex1(item_id),
  INDEX item_keywords_FKIndex2(keyword),
  FOREIGN KEY(item_id)
    REFERENCES item(item_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(keyword)
    REFERENCES keyword(keyword)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE featured_project (
  project_id INTEGER UNSIGNED NOT NULL,
  page_name VARCHAR(25) NULL,
  INDEX featured_projects_FKIndex1(project_id),
  INDEX featured_projects_FKIndex2(page_name),
  FOREIGN KEY(project_id)
    REFERENCES project(project_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(page_name)
    REFERENCES page(page_name)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);


