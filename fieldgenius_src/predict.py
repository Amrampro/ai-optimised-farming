#!/usr/bin/env python3
import argparse
import joblib
import json
import sys
import numpy as np
import pandas as pd

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--culture", required=True, help="Nom de la culture (maïs, haricot, arachide)")
    parser.add_argument("--superficie", type=float, help="Superficie du champ (optionnel)")
    parser.add_argument("--seeds", type=float, help="Quantité de semailles disponible (optionnel)")
    parser.add_argument("--soil_type", default="normal", help="Type de sol : normal, fertile, non_fertile (défaut : normal)")
    parser.add_argument("--use_engrais", type=int, default=0, help="1 si engrais utilisé, 0 sinon (défaut : 0)")
    args = parser.parse_args()

    # Vérifier qu'au moins superficie ou seeds est fourni
    if args.superficie is None and args.seeds is None:
        print(json.dumps({"error": "Veuillez fournir soit la superficie, soit la quantité de semailles (seeds)."}))
        sys.exit(1)

    # Charger le dictionnaire de modèles et paramètres
    try:
        models_dict = joblib.load('ai_model.pkl')
    except Exception as e:
        print(json.dumps({"error": f"Impossible de charger 'models.pkl' : {str(e)}"}))
        sys.exit(1)

    # Vérifier la culture demandée
    if args.culture not in models_dict:
        print(json.dumps({"error": f"Culture '{args.culture}' non reconnue dans models.pkl"}))
        sys.exit(1)

    # Récupérer le sous-dictionnaire de la culture
    culture_data = models_dict[args.culture]
    model = culture_data['model']        # Modèle scikit-learn
    base_seed_rate = culture_data['seed_rate']
    
    # Récupérer la période et les paramètres métier
    periods = models_dict.get('periods', {})
    soil_modifiers = models_dict.get('soil_modifiers', {})
    engrais_range = models_dict.get('engrais_range', (1.2, 1.4))

    # Récupérer la période de semis (ou 'Période inconnue' si non trouvée)
    period = periods.get(args.culture, "Période inconnue")

    # Mapping pour nom de la colonne d'entrée
    col_map = {
        'maïs': 'maisin_final',
        'haricot': 'haricotin_final',
        'arachide': 'arachidein_final'
    }
    if args.culture not in col_map:
        print(json.dumps({"error": f"Impossible de déterminer la colonne d'entrée pour {args.culture}."}))
        sys.exit(1)
    input_col_name = col_map[args.culture]

    # Calcul du nombre de semences et prédiction initiale
    if args.superficie is not None:
        seeds_needed = base_seed_rate * args.superficie
        X_test = pd.DataFrame({input_col_name: [seeds_needed]})
        predicted_yield = model.predict(X_test)[0]
        superficie_val = args.superficie
    else:
        seeds_needed = args.seeds
        X_test = pd.DataFrame({input_col_name: [args.seeds]})
        predicted_yield = model.predict(X_test)[0]
        superficie_val = args.seeds / base_seed_rate

    # Vérifier le type de sol
    if args.soil_type not in soil_modifiers:
        print(json.dumps({"error": f"Type de sol '{args.soil_type}' non reconnu."}))
        sys.exit(1)

    min_soil, max_soil = soil_modifiers[args.soil_type]
    soil_factor = np.random.uniform(min_soil, max_soil)
    predicted_yield *= soil_factor

    # Appliquer l'engrais si use_engrais = 1
    use_engrais = (args.use_engrais == 1)
    if use_engrais:
        engrais_min, engrais_max = engrais_range
        engrais_factor = np.random.uniform(engrais_min, engrais_max)
        predicted_yield *= engrais_factor

    # Construire le résultat
    result = {
        "culture": args.culture,
        "period": period,
        "soil_type": args.soil_type,
        "use_engrais": use_engrais,
        "superficie": superficie_val,
        "seeds_needed": seeds_needed,
        "predicted_yield": predicted_yield
    }

    # Afficher en JSON
    print(json.dumps(result, ensure_ascii=False))

if __name__ == "__main__":
    main()
