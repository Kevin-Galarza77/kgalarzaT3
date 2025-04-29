using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KGalarzaT3.Model
{
    public class Person
    {
        public string name { get; set; }
        public string lastName{ get; set; }
        public string? typeCi { get; set; }
        public string ci { get; set; }
        public string email { get; set; }
        public decimal salary { get; set; }
        public DateTime bornDate { get; set; }
        public decimal inputIESS { get; set; }

        public Person(string name, string lastname, string? typeCi, string ci, string email, decimal salary, DateTime bornDate) {
            this.name = name;
            this.lastName = lastname;
            this.typeCi = typeCi;
            this.ci = ci;
            this.email = email;
            this.salary = salary;
            this.bornDate = bornDate;
            this.inputIESS = this.salary * 0.0945m;
        }

    }
}
