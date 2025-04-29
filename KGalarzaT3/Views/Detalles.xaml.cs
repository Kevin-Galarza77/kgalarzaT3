using KGalarzaT3.Model;
namespace KGalarzaT3.Views;

public partial class Detalles : ContentPage
{
	public Detalles(Person person)
	{
		InitializeComponent();
		nameText.Text = person.name;
		lastNameText.Text = person.lastName;
		ciText.Text = person.ci;
		ciTypeText.Text = person.typeCi;
        emailText.Text = person.email;
		salaryText.Text = person.salary.ToString();
        bornTxt.Text = person.bornDate.ToString();
		inputIESSText.Text = person.inputIESS.ToString(); 
    }

}