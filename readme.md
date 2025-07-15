# Formulaire & Suivi Trello

## 🔧 Configuration Trello API

Pour connecter votre application Symfony à Trello, vous devez renseigner les variables suivantes dans votre fichier `.env.local` :

```dotenv
TRELLO_KEY=""
TRELLO_TOKEN=""
TRELLO_LIST_ID=""
```
le link :https://trello.com/1/authorize?key=847908c9723a1055e85784444b8e4505&name=SymfonyFormApp&scope=read,write&expiration=never&response_type=token

---

### ✅ 1. `TRELLO_KEY` – Clé API

👉 Rendez-vous sur :  
🔗 <https://trello.com/app-key>

- Connectez-vous à votre compte Trello.
- Votre **clé API** s'affichera directement.
- Cliquez ensuite sur le lien **"Token"** juste en dessous pour générer votre token.

---

### ✅ 2. `TRELLO_TOKEN` – Token utilisateur

- Toujours sur <https://trello.com/app-key>, cherchez le lien :

  > *To generate a token, click here*

- Cliquez dessus pour générer votre **token utilisateur**.
- Autorisez l'accès avec les permissions `read, write` et **aucune date d'expiration**, si demandé.

> 🔐 Ce token est personnel et donne un accès complet à votre compte Trello. **Ne le partagez pas publiquement.**

---

### ✅ 3. `TRELLO_LIST_ID` – ID de la liste Trello cible

Vous avez besoin de l’ID de la **liste** dans laquelle vous souhaitez créer vos cartes Trello.

#### 🔍 Option 1 — via l’URL d'une carte déjà existante

- Ouvrez une carte dans la liste concernée.
- L’URL sera du type :

<https://trello.com/c/XXXXX/123-nom-de-la-carte>

- Cliquez ensuite sur les **3 points** dans le coin supérieur droit de la liste → **Copier le lien vers cette liste**
- Vous obtiendrez un lien comme :

<https://trello.com/b/BOARD_ID/board-name/lists/LIST_ID>

#### 🔍 Option 2 — via l'API Trello

Vous pouvez lister les listes d’un tableau avec cette requête :

```bash
curl "https://api.trello.com/1/boards/BOARD_ID/lists?key=TRELLO_KEY&token=TRELLO_TOKEN"
```

Cela vous retournera un tableau JSON avec les `id` et `name` de chaque liste.

##Installation

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```
