/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  20/10/2021 11:01:59                      */
/*==============================================================*/


drop table if exists Activite;

drop table if exists Administrateur;

drop table if exists Coach;

drop table if exists Cours;

drop table if exists Donner;

drop table if exists Etre_Ami;

drop table if exists Groupe;

drop table if exists Materiel;

drop table if exists Membre;

drop table if exists Message;

drop table if exists Participer;

drop table if exists Prendre;

drop table if exists Sportif;

drop table if exists Utilisateur;

/*==============================================================*/
/* Table : Activite                                             */
/*==============================================================*/
create table Activite
(
   idEvent              int not null,
   title                varchar(128),
   description          text,
   eventDate            datetime,
   place                varchar(32),
   primary key (idEvent)
);

/*==============================================================*/
/* Table : Administrateur                                       */
/*==============================================================*/
create table Administrateur
(
   idUser               int not null,
   username             varchar(32),
   password             varchar(128),
   lastName             varchar(32),
   firstName            varchar(32),
   city                 varchar(32),
   address              longtext,
   zipCode              char(5),
   profilePicture       longblob,
   primary key (idUser)
);

/*==============================================================*/
/* Table : Coach                                                */
/*==============================================================*/
create table Coach
(
   idUser               int not null,
   username             varchar(32),
   password             varchar(128),
   lastName             varchar(32),
   firstName            varchar(32),
   city                 varchar(32),
   address              longtext,
   zipCode              char(5),
   profilePicture       longblob,
   primary key (idUser)
);

/*==============================================================*/
/* Table : Cours                                                */
/*==============================================================*/
create table Cours
(
   IdCours              int not null,
   title                varchar(128),
   publishedDate        datetime,
   skillLevel           char(1),
   primary key (IdCours)
);

/*==============================================================*/
/* Table : Donner                                               */
/*==============================================================*/
create table Donner
(
   IdCours              int not null,
   idUser               int not null,
   primary key (IdCours, idUser)
);

/*==============================================================*/
/* Table : Etre_Ami                                             */
/*==============================================================*/
create table Etre_Ami
(
   Uti_idUser           int not null,
   idUser               int not null,
   publishedDate        datetime,
   primary key (Uti_idUser, idUser)
);

/*==============================================================*/
/* Table : Groupe                                               */
/*==============================================================*/
create table Groupe
(
   idGroup              int not null,
   title                varchar(128),
   primary key (idGroup)
);

/*==============================================================*/
/* Table : Materiel                                             */
/*==============================================================*/
create table Materiel
(
   idMaterial           int not null,
   idUser               int not null,
   title                varchar(128),
   description          text,
   price                float(5,2),
   primary key (idMaterial)
);

/*==============================================================*/
/* Table : Membre                                               */
/*==============================================================*/
create table Membre
(
   idUser               int not null,
   idGroup              int not null,
   primary key (idUser, idGroup)
);

/*==============================================================*/
/* Table : Message                                              */
/*==============================================================*/
create table Message
(
   idMessage            int not null,
   idGroup              int not null,
   idUser               int not null,
   content              text,
   publishedDate        datetime,
   primary key (idMessage)
);

/*==============================================================*/
/* Table : Participer                                           */
/*==============================================================*/
create table Participer
(
   idUser               int not null,
   idEvent              int not null,
   primary key (idUser, idEvent)
);

/*==============================================================*/
/* Table : Prendre                                              */
/*==============================================================*/
create table Prendre
(
   idUser               int not null,
   IdCours              int not null,
   primary key (idUser, IdCours)
);

/*==============================================================*/
/* Table : Sportif                                              */
/*==============================================================*/
create table Sportif
(
   idUser               int not null,
   username             varchar(32),
   password             varchar(128),
   lastName             varchar(32),
   firstName            varchar(32),
   city                 varchar(32),
   address              longtext,
   zipCode              char(5),
   profilePicture       longblob,
   primary key (idUser)
);

/*==============================================================*/
/* Table : Utilisateur                                          */
/*==============================================================*/
create table Utilisateur
(
   idUser               int not null,
   username             varchar(32),
   password             varchar(128),
   lastName             varchar(32),
   firstName            varchar(32),
   city                 varchar(32),
   address              longtext,
   zipCode              char(5),
   profilePicture       longblob,
   primary key (idUser)
);

alter table Administrateur add constraint FK_H_Utilisateur3 foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Coach add constraint FK_H_Utilisateur foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Donner add constraint FK_Donner foreign key (IdCours)
      references Cours (IdCours) on delete restrict on update restrict;

alter table Donner add constraint FK_Donner2 foreign key (idUser)
      references Coach (idUser) on delete restrict on update restrict;

alter table Etre_Ami add constraint FK_Etre_Ami foreign key (Uti_idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Etre_Ami add constraint FK_Etre_Ami2 foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Materiel add constraint FK_Echanger foreign key (idUser)
      references Sportif (idUser) on delete restrict on update restrict;

alter table Membre add constraint FK_Membre foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Membre add constraint FK_Membre2 foreign key (idGroup)
      references Groupe (idGroup) on delete restrict on update restrict;

alter table Message add constraint FK_Envoyer foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

alter table Message add constraint FK_Inclure foreign key (idGroup)
      references Groupe (idGroup) on delete restrict on update restrict;

alter table Participer add constraint FK_Participer foreign key (idUser)
      references Sportif (idUser) on delete restrict on update restrict;

alter table Participer add constraint FK_Participer2 foreign key (idEvent)
      references Activite (idEvent) on delete restrict on update restrict;

alter table Prendre add constraint FK_Prendre foreign key (idUser)
      references Sportif (idUser) on delete restrict on update restrict;

alter table Prendre add constraint FK_Prendre2 foreign key (IdCours)
      references Cours (IdCours) on delete restrict on update restrict;

alter table Sportif add constraint FK_H_Utilisateur2 foreign key (idUser)
      references Utilisateur (idUser) on delete restrict on update restrict;

