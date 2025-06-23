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
fieldgenius/
│
├── assets/            # Images et fichiers CSS
│   └── *.jpg, *.css   # Ressources pour l'interface
│
├── ai_model.pkl       # Modèle IA sérialisé (avec joblib)
├── predict.py         # Script Python pour exécuter les prédictions
├── index.php          # Interface web HTML + PHP
├── README.md          # Fichier de documentation (ce fichier)
