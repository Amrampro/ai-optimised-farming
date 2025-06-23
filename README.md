# ai-optimised-farming
## 🌾 FieldGenius - Système intelligent d'aide à la plantation agricole

**FieldGenius** est une application intelligente qui aide les agriculteurs à planifier efficacement leurs semailles. Grâce à un modèle d'intelligence artificielle entraîné sur des données locales, elle prédit le rendement agricole attendu selon la culture, la superficie, le type de sol et l’usage d’engrais.

---

## 🚀 Fonctionnalités clés

- ✅ Prédiction de rendement pour **maïs**, **haricot**, et **arachide**
- 📐 Calcul basé sur la **superficie** ou la **quantité de semences**
- 🌍 Ajustement du rendement selon :
  - **Type de sol** (normal, fertile, non fertile)
  - **Utilisation d’engrais**
- 📆 Affichage de la **période idéale de semis**
- 🖥️ Interface en PHP connectée à un modèle Python via `predict.py`

---

## 📁 Structure du projet

```bash
fieldgenius_src/
│
├── assets/            # Images et fichiers CSS
│   └── *.jpg, *.css   # Ressources pour l'interface
│
├── ai_model.pkl       # Modèle IA sérialisé (avec joblib)
├── predict.py         # Script Python pour exécuter les prédictions
├── index.php          # Interface web HTML + PHP
```

## ⚙️ Technologies utilisées

- Python 3 (IA & traitement)
  scikit-learn, pandas, numpy, joblib
- PHP (serveur web)
- HTML/CSS/JavaScript (interface utilisateur)
- XAMPP / Apache (exécution en local)
- Google Colab (entraînement initial du modèle)

## 🔧 Installation locale

### 1. Prérequis
- XAMPP ou équivalent (Apache + PHP)
- Python installé localement
- Modules Python requis :
```bash
pip install scikit-learn pandas numpy joblib
```

### 2. Déploiement
- Copier le dossier fieldgenius_src/ dans htdocs (XAMPP)
- Démarrer Apache dans XAMPP
- Accéder à http://localhost/fieldgenius_src

## 🧪 Utilisation

- Ouvrir index.php dans le navigateur.
- Sélectionner la culture (maïs, haricot ou arachide)
- Entrer la superficie OU la quantité de semences
- Choisir le type de sol et indiquer si engrais est utilisé
- Cliquer sur Calculer
- Obtenez un plan complet de semis, y compris :
    (Semences nécessaires,
    Rendement estimé,
    Période de semaille)
