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
-- Data for Name: address; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (1, 1, '', '');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (2, 1, '', '');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (3, 2, '', '');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (4, 3, '', '');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (5, 4, '', '');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (6, 7, 'Школьная', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (7, 7, 'Школьная', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (8, 7, 'Школьная', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (9, 8, 'Школьная', 'Work');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (10, 4, 'Крутая', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (11, 4, 'Крутая', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (12, 10, 'Школьная', 'Work');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (14, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (16, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (19, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (21, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (22, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (23, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (24, 10, 'Улица спида', 'Work');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (25, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (26, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (27, 11, 'Это адрес контакта', 'Home');
INSERT INTO public.address (id_address, id_recipient, address, status) VALUES (28, 11, 'Это адрес контакта', 'Home');


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

SELECT pg_catalog.setval('public.address_id_address_seq', 28, true);


--
-- Name: contact_list_id_entry_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.contact_list_id_entry_seq', 11, true);


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

