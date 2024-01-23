INSERT INTO Utilisateur (id, nom, prenom, mdp, roles, groupes, Date_Creation, Date_Modification, Mdp_New)
VALUES
('adminProf', 'Lairbon', 'Oussama', '$2y$10$5f6jHhTz48ebDmHMvx7JfeEKrot5kIWyUgrJ88CcJW5qWafrj0/f6', 'AE', 'info1:info2:A:A1:A2', now(), now(), false),
('prof', 'Lami', 'Vanessa', '$2y$10$bYqS7tOWfE5nu79gu5Dr2elXTbGIjsD9dCiwnLCLZoOGc0EtZfbB2', 'E', 'info1:info2:A:A1:A2', now(),now(), false),
('tuteur', 'Obabo', 'Barrab', '$2y$10$iLfLtcQZJ5GyWYjWH265J.a8w9HyTPmBxNPCAgHloexLMJapyfix2', 'T', 'info1:info2:A:A1:A2', now(),now(), false),
('admin', 'Mama', 'Joe', '$2y$10$fs9slCtGpi/BGj0rNyHqj.IKgQizv7KFAA5vfEuxGX48RgbIln5me', 'A', 'info1:info2:A:A1:A2', now(),now(), false);

INSERT INTO Parametres
VALUES
('SeanceMaxParFiltre', 50),
('EvenementMaxParActivite', 5),
('PjMaxParEvenement', 3);