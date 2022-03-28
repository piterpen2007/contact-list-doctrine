--
-- PostgreSQL database dump
--

-- Dumped from database version 14.2
-- Dumped by pg_dump version 14.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: address_status; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.address_status (id, name) VALUES (1, 'Work');
INSERT INTO public.address_status (id, name) VALUES (2, 'Home');


--
-- Data for Name: address; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.address (id_address, address, status_id) VALUES (24, 'Улица спида', 1);
INSERT INTO public.address (id_address, address, status_id) VALUES (12, 'Школьная', 1);
INSERT INTO public.address (id_address, address, status_id) VALUES (9, 'Школьная', 1);
INSERT INTO public.address (id_address, address, status_id) VALUES (5, '', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (4, '', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (3, '', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (2, '', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (1, '', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (28, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (27, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (26, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (25, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (23, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (22, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (21, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (19, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (16, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (14, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (11, 'Крутая', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (10, 'Крутая', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (8, 'Школьная', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (7, 'Школьная', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (6, 'Школьная', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (40, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (41, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (42, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (43, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (44, 'Это адрес контакта', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (45, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (46, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (47, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (48, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (49, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (50, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (51, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (52, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (53, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (54, 'dhdfh', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (55, 'минина', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (56, 'Ждановский, ул.Школьная, д30', 2);
INSERT INTO public.address (id_address, address, status_id) VALUES (57, 'Это адрес контакта', 2);


--
-- Data for Name: recipients; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (1, 'Осипов Геннадий Иванович', '1985-06-15', 'Системный администратор', 2220, 'RUB', 'recipient');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (2, 'Тамара', '1990-06-06', '', 2220, 'RUB', 'recipient');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (3, 'Дамир Авто', '1990-12-01', 'Автомеханик', 4566, 'RUB', 'recipient');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (4, 'Катя', '1989-03-08', '', 1999, 'RUB', 'recipient');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (5, 'Шипенко Леонид Иосифович', '1969-02-07', 'Слесарь', 3543, 'RUB', 'recipient');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (6, 'Дед', '1945-06-04', 'Столяр', 3555, 'RUB', 'kinsfolk');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (7, 'Калинин Пётр Александрович', '1983-06-04', 'Фитнес тренер', 3453, 'RUB', 'customer');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (8, 'Васин Роман Александрович', '1977-01-04', 'Фитнес тренер', 4626, 'RUB', 'customer');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (9, 'Стрелецкая Анастасия Виктоовна', '1980-12-30', 'Админимстратор фитнес центра', 2627, 'RUB', 'customer');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (10, 'Шатов Александр Иванович', '1971-12-02', '', 1111, 'RUB', 'colleague');
INSERT INTO public.recipients (id_recipient, full_name, birthday, profession, amount, currency, type) VALUES (11, 'Наташа', '1984-05-10', '', 9999, 'RUB', 'colleague');


--
-- Data for Name: address_to_recipients; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (1, 1, 1);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (2, 2, 1);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (3, 3, 2);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (4, 4, 3);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (5, 5, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (6, 6, 7);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (7, 7, 7);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (8, 8, 7);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (9, 9, 8);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (10, 10, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (11, 11, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (12, 12, 10);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (13, 14, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (14, 16, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (15, 19, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (16, 21, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (17, 22, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (18, 23, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (19, 24, 10);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (20, 25, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (21, 26, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (22, 27, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (23, 28, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (24, 10, 2);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (25, 10, 3);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (26, 10, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (27, 48, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (28, 48, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (29, 49, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (30, 49, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (31, 50, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (32, 50, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (33, 52, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (34, 52, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (35, 53, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (36, 53, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (37, 54, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (38, 54, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (39, 55, 9);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (40, 55, 6);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (41, 56, 3);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (42, 56, 2);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (43, 56, 5);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (44, 56, 4);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (45, 56, 1);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (46, 56, 11);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (47, 56, 10);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (48, 56, 8);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (49, 56, 9);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (50, 56, 7);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (51, 56, 6);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (52, 57, 10);
INSERT INTO public.address_to_recipients (address_to_recipients_id, id_address, id_recipient) VALUES (53, 57, 2);


--
-- Data for Name: colleagues; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.colleagues (id_recipient, department, "position", room_number) VALUES (10, 'Дирекция', 'Директор', '405');
INSERT INTO public.colleagues (id_recipient, department, "position", room_number) VALUES (11, 'Дирекция', 'Секретарь', '404');


--
-- Data for Name: contact_list; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (1, 1, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (2, 2, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (3, 3, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (4, 4, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (5, 5, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (8, 8, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (9, 9, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (10, 10, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (11, 11, false);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (7, 7, true);
INSERT INTO public.contact_list (id_entry, id_recipient, blacklist) VALUES (6, 6, true);


--
-- Data for Name: customers; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.customers (id_recipient, contract_number, average_transaction_amount, discount, time_to_call) VALUES (7, '5684', 2500, '5%', 'С 9:00 до 13:00 в будни');
INSERT INTO public.customers (id_recipient, contract_number, average_transaction_amount, discount, time_to_call) VALUES (8, '5683', 9500, '10%', 'С 12:00 до 16:00 в будни');
INSERT INTO public.customers (id_recipient, contract_number, average_transaction_amount, discount, time_to_call) VALUES (9, '5682', 15200, '10%', 'С 17:00 до 19:00 в будни');


--
-- Data for Name: email; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.email (id, type_email, email, recipient_id) VALUES (1, 'Yandex', 'pipetka@yandex.ru', 8);
INSERT INTO public.email (id, type_email, email, recipient_id) VALUES (2, 'Google', 'kuku@gmail.com', 3);
INSERT INTO public.email (id, type_email, email, recipient_id) VALUES (3, 'Rambler', 'pochta@rambler.com', 3);


--
-- Data for Name: kinsfolk; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.kinsfolk (id_recipient, status, ringtone, hotkey) VALUES (6, 'Дед', 'Bells', '1');


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users (id, login, password) VALUES (1, 'admin', '$2y$10$wvtXiHCmXEtmDC3rBZrD8eej4ZwiKzaSwtd3.sJJH9v8wxzDGS2DG');


--
-- Name: address_id_address_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.address_id_address_seq', 57, true);


--
-- Name: address_status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.address_status_id_seq', 2, true);


--
-- Name: address_to_recipients_address_to_recipients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.address_to_recipients_address_to_recipients_id_seq', 53, true);


--
-- Name: contact_list_id_entry_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.contact_list_id_entry_seq', 11, true);


--
-- Name: email_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.email_id_seq', 3, true);


--
-- Name: recipients_id_recipient_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recipients_id_recipient_seq', 11, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- PostgreSQL database dump complete
--

