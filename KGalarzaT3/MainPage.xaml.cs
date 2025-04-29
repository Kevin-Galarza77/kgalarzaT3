using System.ComponentModel.DataAnnotations;
using KGalarzaT3.Views;

namespace KGalarzaT3
{
    public partial class MainPage : ContentPage
    {
        int count = 0;

        public MainPage()
        {
            InitializeComponent();
        }

        private async void OnGuardarContactoClicked(object sender, EventArgs e)
        {
            DateTime bornDate = bornDatePicker.Date;
            string lastName = lastNameText.Text;
            string? typeId = typeIdentificationPicker.SelectedItem?.ToString();
            string salary = salaryText.Text;
            string email = emailText.Text;
            string name = nameText.Text;
            string ci = ciText.Text;

            string validate = ValidateForm(name, lastName, email, typeId, ci, salary);

            if (validate == "")
            { 
                await Navigation.PushAsync(new Detalles(new Model.Person(name, lastName, typeId, ci, email, Decimal.Parse(salary), bornDate)));
            }
            else
            {
                await DisplayAlert("Error", validate, "OK");
            }

        }

        private string ValidateForm(string name, string lastName, string email, string? typeId, string ci, string salary)
        {
            if (string.IsNullOrWhiteSpace(typeId))
            {
                return "Por favor selecciona un tipo de identificación.";
            }

            if (string.IsNullOrWhiteSpace(ci))
            {
                return "Por favor ingresa el número de identificación.";
            }

            if (!ci.All(char.IsDigit))
            {
                return "La identificación debe contener solo dígitos.";
            }

            if (typeId == "CI" && ci.Length != 10)
            {
                return "El número de CI debe tener 10 dígitos.";
            }

            if (typeId == "RUC" && ci.Length != 13)
            {
                return "El número de RUC debe tener 13 dígitos.";
            }

            if (string.IsNullOrWhiteSpace(name))
            {
                return "Por favor ingresa tu nombre.";
            }

            if (!name.All(c => char.IsLetter(c) || char.IsWhiteSpace(c)))
            {
                return "El nombre solo debe contener letras.";
            }

            if (string.IsNullOrWhiteSpace(lastName))
            {
                return "Por favor ingresa tus apellidos.";
            }

            if (!lastName.All(c => char.IsLetter(c) || char.IsWhiteSpace(c)))
            {
                return "Los apellidos solo deben contener letras.";
            }

            if (string.IsNullOrWhiteSpace(email))
            {
                return "Por favor ingresa tu correo electrónico.";
            }

            if (!IsValidEmail(email))
            {
                return "Por favor ingresa un correo electrónico válido.";
            }

            if (string.IsNullOrWhiteSpace(salary))
            {
                return "Por favor ingresa el salario.";
            }

            if (!decimal.TryParse(salary, out decimal salario) || salario < 0)
            {
                return "El salario debe ser un número positivo.";
            }

            return "";   
        }

        private bool IsValidEmail(string email)
        {
            return System.Text.RegularExpressions.Regex.IsMatch(email, @"^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$");
        }

        private void OnTextToUpper(object sender, TextChangedEventArgs e)
        {
            var entry = sender as Entry;

            if (entry == null || string.IsNullOrEmpty(e.NewTextValue))
                return;

            int cursorPos = entry.CursorPosition;
            entry.Text = e.NewTextValue.ToUpper();

            entry.CursorPosition = entry.Text.Length;
        }

        private void OnTextToLower(object sender, TextChangedEventArgs e)
        {
            var entry = sender as Entry;

            if (entry == null || string.IsNullOrEmpty(e.NewTextValue))
                return;

            int cursorPos = entry.CursorPosition;
            entry.Text = e.NewTextValue.ToLower();

            entry.CursorPosition = entry.Text.Length;
        }


    }

}
