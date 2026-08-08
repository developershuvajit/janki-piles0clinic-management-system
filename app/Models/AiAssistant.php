<?php

namespace App\Models;

class AiAssistant
{
    /**
     * Analyze symptoms and return diagnostic recommendations.
     */
    public static function recommend(string $symptoms): array
    {
        $text = strtolower(trim($symptoms));

        // 1. Proctology - Piles / Hemorrhoids (Bawasir / Bleeding / Swelling / Mass)
        if (str_contains($text, 'pile') || str_contains($text, 'hemorrhoid') || str_contains($text, 'bawasir') || str_contains($text, 'bleeding in stool') || str_contains($text, 'rectal bleeding') || str_contains($text, 'mass')) {
            return [
                'diagnosis' => 'Internal / External Hemorrhoids (Arsh / Piles)',
                'prescription' => "Tab Arshoghni Vati 2 Tab BD (After meal with warm water, 7 days)\nTab Pilex / Anovate 1 BD (7 days)\nAnovate Ointment (Apply locally before & after bowel movement)",
                'advice' => 'Take Warm Sitz bath twice daily for 15 mins. Consume high fiber diet & 3-4L water daily. Avoid sitting for long hours & avoid spicy food.'
            ];
        }

        // 2. Proctology - Anal Fissure (Severe Anal Pain / Burning after stool)
        if (str_contains($text, 'fissure') || str_contains($text, 'anal pain') || str_contains($text, 'burning stool') || str_contains($text, 'cutting pain')) {
            return [
                'diagnosis' => 'Acute / Chronic Anal Fissure (Parikartika)',
                'prescription' => "Tab Paracetamol 650mg BD (For pain relief, 3 days)\nOintment Lignocaine 2% + Diltiazem cream (Apply locally 15 mins before stool)\nSyrup Lactulose 15ml at bedtime (For soft stool, 5 days)",
                'advice' => 'Warm Sitz bath with Betadine/warm water 3 times daily. Do not strain during bowel movements. Strict high-fiber diet & fluids.'
            ];
        }

        // 3. Proctology - Anal Fistula / Abscess (Pus discharge / Boil near anus)
        if (str_contains($text, 'fistula') || str_contains($text, 'pus') || str_contains($text, 'discharge') || str_contains($text, 'boil') || str_contains($text, 'abscess')) {
            return [
                'diagnosis' => 'Fistula-in-Ano / Perianal Abscess (Bhagandar)',
                'prescription' => "Cap Amoxyclav 625mg BD (After meal, 5 days)\nTab Chymoral Forte 1 BD (For inflammation, 5 days)\nTab Zero-P (Aceclofenac + Paracetamol) BD (For pain, 3 days)",
                'advice' => 'Sitz bath twice daily with Povidone Iodine. Maintain perianal hygiene. Surgical / Kshar Sutra evaluation recommended.'
            ];
        }

        // 4. Constipation / Indigestion (Koshthabaddhata)
        if (str_contains($text, 'constipation') || str_contains($text, 'hard stool') || str_contains($text, 'indigestion') || str_contains($text, 'bloating')) {
            return [
                'diagnosis' => 'Chronic Constipation & Dyspepsia (Koshthabaddhata)',
                'prescription' => "Isabgol Husk (Psyllium) 2 tsp with lukewarm water at bedtime\nTab Triphala Churna 1 tsp or 2 tabs at bedtime\nTab Pan-40 (Pantoprazole 40mg) OD (Before breakfast, 5 days)",
                'advice' => 'Increase dietary fiber (green vegetables, oats, fruits). Drink at least 3-4 liters of water daily. Daily 30-min walk.'
            ];
        }

        // 5. Cardiac / Emergency Symptoms
        if (str_contains($text, 'chest pain') || str_contains($text, 'breath') || str_contains($text, 'cardiac') || str_contains($text, 'palpitation')) {
            return [
                'diagnosis' => 'Acute Coronary Syndrome / Anginal Pain Evaluation',
                'prescription' => "Tab Aspirin 150mg (Stat - Chew)\nTab Clopidogrel 75mg (Stat)\nTab Sorbitrate 5mg sublingual (in emergency)",
                'advice' => 'Restrict physical activity immediately. Immediate ECG & Troponin test recommended. Refer to Emergency / ICU Ward.'
            ];
        }

        // 6. Flu / Fever / Viral Infection
        if (str_contains($text, 'fever') || str_contains($text, 'cough') || str_contains($text, 'cold') || str_contains($text, 'flu') || str_contains($text, 'throat')) {
            return [
                'diagnosis' => 'Acute Viral Pyrexia / Influenza Infection',
                'prescription' => "Tab Paracetamol 650mg TDS (After meal, 3 days)\nTab Cetirizine 10mg OD (At bedtime, 5 days)\nSyr Dextromethorphan 10ml TDS (5 days)",
                'advice' => 'Drink warm water & fluids. Steam inhalation twice daily. Salt water gargle 3 times daily. Adequate rest.'
            ];
        }

        // 7. Dental Abscess / Pain
        if (str_contains($text, 'tooth') || str_contains($text, 'dental') || str_contains($text, 'gum') || str_contains($text, 'jaw')) {
            return [
                'diagnosis' => 'Dental Abscess / Gingival Infection',
                'prescription' => "Cap Amoxicillin 500mg TDS (For 5 days)\nTab Metronidazole 400mg TDS (For 5 days)\nTab Ibuprofen 400mg BD (For pain, 3 days)",
                'advice' => 'Warm saline rinses 4-5 times daily. Avoid hard or sweet food items. Consult a dentist immediately.'
            ];
        }

        // 8. Dermatitis / Skin Allergy
        if (str_contains($text, 'skin') || str_contains($text, 'itch') || str_contains($text, 'rash') || str_contains($text, 'allergy')) {
            return [
                'diagnosis' => 'Allergic Dermatitis / Cutaneous Hypersensitivity',
                'prescription' => "Tab Levocetirizine 5mg OD (At bedtime, 5 days)\nCream Hydrocortisone 1% (Apply locally BD, 5 days)\nLotion Calamine (Apply locally for soothing)",
                'advice' => 'Avoid potential contact allergens (harsh soaps, cosmetics). Wear loose cotton clothes. Do not scratch.'
            ];
        }

        // 9. Gastric / Hyperacidity / Vomiting
        if (str_contains($text, 'stomach') || str_contains($text, 'gastric') || str_contains($text, 'acidity') || str_contains($text, 'vomit') || str_contains($text, 'nausea')) {
            return [
                'diagnosis' => 'Acute Gastritis / Gastroesophageal Reflux',
                'prescription' => "Tab Pantoprazole 40mg OD (30 mins before breakfast, 5 days)\nTab Domperidone 10mg TDS (For nausea, 3 days)\nORS Sachet (1 packet in 1L water, sip regularly)",
                'advice' => 'Eat a bland diet (boiled rice, curd, banana). Avoid spicy, oily, or fried foods. Maintain body hydration.'
            ];
        }

        // 10. High Blood Pressure / Hypertension
        if (str_contains($text, 'bp') || str_contains($text, 'hypertension') || str_contains($text, 'pressure') || str_contains($text, 'giddiness') || str_contains($text, 'dizziness')) {
            return [
                'diagnosis' => 'Essential Hypertension / Blood Pressure Elevation',
                'prescription' => "Tab Amlodipine 5mg OD (Morning, after BP check)\nTab Paracetamol 650mg (If headache present)",
                'advice' => 'Low salt diet (<5g/day). Monitor BP morning & evening. Avoid stress and caffeinated drinks. Regular walking.'
            ];
        }

        // 11. Body Pain / Joint Pain / Headache
        if (str_contains($text, 'headache') || str_contains($text, 'body pain') || str_contains($text, 'joint') || str_contains($text, 'back pain')) {
            return [
                'diagnosis' => 'Myalgia / Tension Headache / Musculoskeletal Pain',
                'prescription' => "Tab Aceclofenac 100mg + Paracetamol 325mg BD (After meal, 3 days)\nTab Pantoprazole 40mg OD (Before breakfast, 3 days)\nVolini / Diclofenac Gel (Apply locally on affected area)",
                'advice' => 'Hot water bag compress on affected muscle/joint. Avoid heavy weight lifting. Proper ergonomic posture during work.'
            ];
        }

        // Improved Fallback Recommendation
        return [
            'diagnosis' => 'General OPD Clinical Assessment & Observation',
            'prescription' => "Tab Paracetamol 650mg SOS (For discomfort / fever)\nTab Pantoprazole 40mg OD (Before breakfast, 3 days)\nMultivitamin & Zinc Tab 1 OD (15 days)",
            'advice' => 'Monitor vital parameters (Temperature, Pulse, BP). Maintain hydrated diet & rest. Follow up with consulting specialist if symptoms persist.'
        ];
    }
}

