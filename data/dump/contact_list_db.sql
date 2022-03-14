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

ALTER TABLE ONLY public.kinsfolk DROP CONSTRAINT kinsfolk_recipients_id_recipient_fk;
ALTER TABLE ONLY public.customers DROP CONSTRAINT customers_recipients_id_recipient_fk;
ALTER TABLE ONLY public.contact_list DROP CONSTRAINT contact_list_recipients_id_recipient_fk;
ALTER TABLE ONLY public.colleagues DROP CONSTRAINT colleagues_recipients_id_recipient_fk;
ALTER TABLE ONLY public.address DROP CONSTRAINT address_recipients_id_recipient_fk;
DROP INDEX public.recipients_profession_index;
DROP INDEX public.recipients_full_name_index;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_pk;
ALTER TABLE ONLY public.recipients DROP CONSTRAINT recipients_pk;
ALTER TABLE ONLY public.kinsfolk DROP CONSTRAINT kinsfolk_pk;
ALTER TABLE ONLY public.customers DROP CONSTRAINT customers_pk;
ALTER TABLE ONLY public.contact_list DROP CONSTRAINT contact_list_pk;
ALTER TABLE ONLY public.colleagues DROP CONSTRAINT colleagues_pk;
ALTER TABLE ONLY public.address DROP CONSTRAINT address_pk;
ALTER TABLE public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.contact_list ALTER COLUMN id_entry DROP DEFAULT;
DROP SEQUENCE public.users_id_seq;
DROP TABLE public.users;
DROP TABLE public.recipients;
DROP TABLE public.kinsfolk;
DROP TABLE public.customers;
DROP SEQUENCE public.contact_list_id_entry_seq;
DROP TABLE public.contact_list;
DROP TABLE public.colleagues;
DROP TABLE public.address;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: address; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.address (
    id_address integer NOT NULL,
    id_recipient integer NOT NULL,
    address character varying(255),
    status character varying(4)
);


ALTER TABLE public.address OWNER TO postgres;

--
-- Name: address_id_address_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.address ALTER COLUMN id_address ADD GENERATED BY DEFAULT AS IDENTITY (
    SEQUENCE NAME public.address_id_address_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: colleagues; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.colleagues (
    id_recipient integer NOT NULL,
    department character varying(50),
    "position" character varying(50),
    room_number character varying(3)
);


ALTER TABLE public.colleagues OWNER TO postgres;

--
-- Name: contact_list; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.contact_list (
    id_entry integer NOT NULL,
    id_recipient integer NOT NULL,
    blacklist boolean NOT NULL
);


ALTER TABLE public.contact_list OWNER TO postgres;

--
-- Name: contact_list_id_entry_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.contact_list_id_entry_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.contact_list_id_entry_seq OWNER TO postgres;

--
-- Name: contact_list_id_entry_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.contact_list_id_entry_seq OWNED BY public.contact_list.id_entry;


--
-- Name: customers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customers (
    id_recipient integer NOT NULL,
    contract_number character varying(10),
    average_transaction_amount integer,
    discount character varying(4),
    time_to_call character varying(100)
);


ALTER TABLE public.customers OWNER TO postgres;

--
-- Name: kinsfolk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kinsfolk (
    id_recipient integer NOT NULL,
    status character varying(50),
    ringtone character varying(50),
    hotkey character varying(3)
);


ALTER TABLE public.kinsfolk OWNER TO postgres;

--
-- Name: recipients; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recipients (
    id_recipient integer NOT NULL,
    full_name character varying(100),
    birthday date,
    profession character varying(60),
    amount integer,
    currency character varying(3),
    type character varying(10) NOT NULL
);


ALTER TABLE public.recipients OWNER TO postgres;

--
-- Name: recipients_id_recipient_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.recipients ALTER COLUMN id_recipient ADD GENERATED BY DEFAULT AS IDENTITY (
    SEQUENCE NAME public.recipients_id_recipient_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    login character varying(50),
    password character varying(60)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: contact_list id_entry; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contact_list ALTER COLUMN id_entry SET DEFAULT nextval('public.contact_list_id_entry_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: address address_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.address
    ADD CONSTRAINT address_pk PRIMARY KEY (id_address);


--
-- Name: colleagues colleagues_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.colleagues
    ADD CONSTRAINT colleagues_pk PRIMARY KEY (id_recipient);


--
-- Name: contact_list contact_list_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contact_list
    ADD CONSTRAINT contact_list_pk PRIMARY KEY (id_entry);


--
-- Name: customers customers_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_pk PRIMARY KEY (id_recipient);


--
-- Name: kinsfolk kinsfolk_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kinsfolk
    ADD CONSTRAINT kinsfolk_pk PRIMARY KEY (id_recipient);


--
-- Name: recipients recipients_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recipients
    ADD CONSTRAINT recipients_pk PRIMARY KEY (id_recipient);


--
-- Name: users users_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);


--
-- Name: recipients_full_name_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX recipients_full_name_index ON public.recipients USING btree (full_name);


--
-- Name: recipients_profession_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX recipients_profession_index ON public.recipients USING btree (profession);


--
-- Name: address address_recipients_id_recipient_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.address
    ADD CONSTRAINT address_recipients_id_recipient_fk FOREIGN KEY (id_recipient) REFERENCES public.recipients(id_recipient) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: colleagues colleagues_recipients_id_recipient_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.colleagues
    ADD CONSTRAINT colleagues_recipients_id_recipient_fk FOREIGN KEY (id_recipient) REFERENCES public.recipients(id_recipient) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contact_list contact_list_recipients_id_recipient_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contact_list
    ADD CONSTRAINT contact_list_recipients_id_recipient_fk FOREIGN KEY (id_recipient) REFERENCES public.recipients(id_recipient) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: customers customers_recipients_id_recipient_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_recipients_id_recipient_fk FOREIGN KEY (id_recipient) REFERENCES public.recipients(id_recipient) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: kinsfolk kinsfolk_recipients_id_recipient_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kinsfolk
    ADD CONSTRAINT kinsfolk_recipients_id_recipient_fk FOREIGN KEY (id_recipient) REFERENCES public.recipients(id_recipient) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

