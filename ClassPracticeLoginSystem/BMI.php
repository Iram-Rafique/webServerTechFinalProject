<?php
// run calculation only after form submission
$bmiResult = "";
$category = "";
$weightMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $weight = floatval($_POST["weight"]);
    $heightInCM = floatval($_POST["height"]);

    // convert height to meters
    $heightInM = $heightInCM / 100;

    // calculate bmi
    $bmi = $weight / ($heightInM * $heightInM);
    $bmiRounded = number_format($bmi, 2);

    // category
    if ($bmi < 18.5) {
        $category = "Underweight";
    } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
        $category = "Healthy Weight";
    } elseif ($bmi >= 25 && $bmi <= 29.9) {
        $category = "Overweight";
    } else {
        $category = "Obese";
    }

    //  weight adjustment
    $minHealthyBMI = 18.5;
    $maxHealthyBMI = 24.9;

    $minHealthyWeight = $minHealthyBMI * ($heightInM * $heightInM);
    $maxHealthyWeight = $maxHealthyBMI * ($heightInM * $heightInM);

    if ($bmi < 18.5) {
        $needed = $minHealthyWeight - $weight;
        $weightMessage = "You need to gain " . number_format($needed, 2) . " kg to reach a healthy weight.";
    } elseif ($bmi > 24.9) {
        $needed = $weight - $maxHealthyWeight;
        $weightMessage = "You need to lose " . number_format($needed, 2) . " kg to reach a healthy weight.";
    }

    $bmiResult = "Your BMI is: $bmiRounded<br>Category: $category<br>$weightMessage";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BMI Calculator</title>
</head>
<body>

<h2>BMI Calculator</h2>

<form method="post">
    <label>Weight (kg):</label><br>
    <input type="number" name="weight" step="0.1" required><br><br>

    <label>Height (cm):</label><br>
    <input type="number" name="height" step="0.1" required><br><br>

    <button type="submit">Calculate BMI</button>
</form>

<?php if ($bmiResult): ?>
    <h3>Result</h3>
    <p><?php echo $bmiResult; ?></p>
<?php endif; ?>

</body>
</html>