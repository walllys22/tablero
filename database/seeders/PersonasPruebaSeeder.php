<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonasPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $personas = [
            ['ci' => '90000001', 'first_name' => 'Camila Rojas Vargas', 'birth_date' => '2021-02-14', 'email' => 'camila.rojas@example.com', 'phone' => '71000001', 'address' => 'Santa Cruz', 'gender' => 'Femenino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000002', 'first_name' => 'Sofia Mamani Quispe', 'birth_date' => '2020-07-09', 'email' => 'sofia.mamani@example.com', 'phone' => '71000002', 'address' => 'La Paz', 'gender' => 'Femenino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000003', 'first_name' => 'Valentina Flores Lima', 'birth_date' => '2019-03-21', 'email' => 'valentina.flores@example.com', 'phone' => '71000003', 'address' => 'Cochabamba', 'gender' => 'Femenino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000004', 'first_name' => 'Isabella Romero Paz', 'birth_date' => '2018-11-05', 'email' => 'isabella.romero@example.com', 'phone' => '71000004', 'address' => 'Tarija', 'gender' => 'Femenino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000005', 'first_name' => 'Martina Salazar Vega', 'birth_date' => '2017-06-18', 'email' => 'martina.salazar@example.com', 'phone' => '71000005', 'address' => 'Sucre', 'gender' => 'Femenino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000006', 'first_name' => 'Luciana Herrera Soto', 'birth_date' => '2016-01-27', 'email' => 'luciana.herrera@example.com', 'phone' => '71000006', 'address' => 'Oruro', 'gender' => 'Femenino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000007', 'first_name' => 'Antonella Medina Rios', 'birth_date' => '2015-09-12', 'email' => 'antonella.medina@example.com', 'phone' => '71000007', 'address' => 'Potosi', 'gender' => 'Femenino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000008', 'first_name' => 'Daniela Vargas Molina', 'birth_date' => '2014-04-30', 'email' => 'daniela.vargas@example.com', 'phone' => '71000008', 'address' => 'Trinidad', 'gender' => 'Femenino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000009', 'first_name' => 'Gabriela Rivero Castro', 'birth_date' => '2013-12-03', 'email' => 'gabriela.rivero@example.com', 'phone' => '71000009', 'address' => 'Cobija', 'gender' => 'Femenino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000010', 'first_name' => 'Paula Aguilar Mendez', 'birth_date' => '2012-08-16', 'email' => 'paula.aguilar@example.com', 'phone' => '71000010', 'address' => 'Santa Cruz', 'gender' => 'Femenino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000011', 'first_name' => 'Natalia Suarez Ortiz', 'birth_date' => '2011-05-24', 'email' => 'natalia.suarez@example.com', 'phone' => '71000011', 'address' => 'La Paz', 'gender' => 'Femenino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000012', 'first_name' => 'Renata Pereira Luna', 'birth_date' => '2010-10-10', 'email' => 'renata.pereira@example.com', 'phone' => '71000012', 'address' => 'Cochabamba', 'gender' => 'Femenino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000013', 'first_name' => 'Alejandra Paredes Arce', 'birth_date' => '2009-02-02', 'email' => 'alejandra.paredes@example.com', 'phone' => '71000013', 'address' => 'Tarija', 'gender' => 'Femenino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000014', 'first_name' => 'Bianca Torres Cabrera', 'birth_date' => '2008-07-19', 'email' => 'bianca.torres@example.com', 'phone' => '71000014', 'address' => 'Sucre', 'gender' => 'Femenino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000015', 'first_name' => 'Carla Gutierrez Duran', 'birth_date' => '2007-03-07', 'email' => 'carla.gutierrez@example.com', 'phone' => '71000015', 'address' => 'Oruro', 'gender' => 'Femenino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000016', 'first_name' => 'Elena Navarro Salinas', 'birth_date' => '2006-11-28', 'email' => 'elena.navarro@example.com', 'phone' => '71000016', 'address' => 'Potosi', 'gender' => 'Femenino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000017', 'first_name' => 'Fernanda Arias Roca', 'birth_date' => '2005-06-11', 'email' => 'fernanda.arias@example.com', 'phone' => '71000017', 'address' => 'Trinidad', 'gender' => 'Femenino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000018', 'first_name' => 'Laura Campos Pinto', 'birth_date' => '2004-01-25', 'email' => 'laura.campos@example.com', 'phone' => '71000018', 'address' => 'Cobija', 'gender' => 'Femenino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000019', 'first_name' => 'Mariana Molina Quiroga', 'birth_date' => '2003-09-04', 'email' => 'mariana.molina@example.com', 'phone' => '71000019', 'address' => 'Santa Cruz', 'gender' => 'Femenino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000020', 'first_name' => 'Andrea Cespedes Beltran', 'birth_date' => '2002-04-13', 'email' => 'andrea.cespedes@example.com', 'phone' => '71000020', 'address' => 'La Paz', 'gender' => 'Femenino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000021', 'first_name' => 'Noelia Ibarra Lopez', 'birth_date' => '2001-12-01', 'email' => 'noelia.ibarra@example.com', 'phone' => '71000021', 'address' => 'Cochabamba', 'gender' => 'Femenino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000022', 'first_name' => 'Ximena Delgadillo Mora', 'birth_date' => '2000-08-22', 'email' => 'ximena.delgadillo@example.com', 'phone' => '71000022', 'address' => 'Tarija', 'gender' => 'Femenino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000023', 'first_name' => 'Rocio Villca Mercado', 'birth_date' => '1999-05-15', 'email' => 'rocio.villca@example.com', 'phone' => '71000023', 'address' => 'Sucre', 'gender' => 'Femenino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000024', 'first_name' => 'Patricia Zambrana Nunez', 'birth_date' => '1998-10-06', 'email' => 'patricia.zambrana@example.com', 'phone' => '71000024', 'address' => 'Oruro', 'gender' => 'Femenino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000025', 'first_name' => 'Veronica Ortega Molina', 'birth_date' => '1996-06-20', 'email' => 'veronica.ortega@example.com', 'phone' => '71000025', 'address' => 'Potosi', 'gender' => 'Femenino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000026', 'first_name' => 'Mateo Rojas Vargas', 'birth_date' => '2021-01-16', 'email' => 'mateo.rojas@example.com', 'phone' => '72000001', 'address' => 'Santa Cruz', 'gender' => 'Masculino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000027', 'first_name' => 'Santiago Mamani Quispe', 'birth_date' => '2020-06-08', 'email' => 'santiago.mamani@example.com', 'phone' => '72000002', 'address' => 'La Paz', 'gender' => 'Masculino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000028', 'first_name' => 'Lucas Flores Lima', 'birth_date' => '2019-04-19', 'email' => 'lucas.flores@example.com', 'phone' => '72000003', 'address' => 'Cochabamba', 'gender' => 'Masculino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000029', 'first_name' => 'Benjamin Romero Paz', 'birth_date' => '2018-12-02', 'email' => 'benjamin.romero@example.com', 'phone' => '72000004', 'address' => 'Tarija', 'gender' => 'Masculino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000030', 'first_name' => 'Thiago Salazar Vega', 'birth_date' => '2017-07-17', 'email' => 'thiago.salazar@example.com', 'phone' => '72000005', 'address' => 'Sucre', 'gender' => 'Masculino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000031', 'first_name' => 'Nicolas Herrera Soto', 'birth_date' => '2016-02-26', 'email' => 'nicolas.herrera@example.com', 'phone' => '72000006', 'address' => 'Oruro', 'gender' => 'Masculino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000032', 'first_name' => 'Gabriel Medina Rios', 'birth_date' => '2015-08-14', 'email' => 'gabriel.medina@example.com', 'phone' => '72000007', 'address' => 'Potosi', 'gender' => 'Masculino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000033', 'first_name' => 'Emiliano Vargas Molina', 'birth_date' => '2014-05-29', 'email' => 'emiliano.vargas@example.com', 'phone' => '72000008', 'address' => 'Trinidad', 'gender' => 'Masculino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000034', 'first_name' => 'Joaquin Rivero Castro', 'birth_date' => '2013-11-02', 'email' => 'joaquin.rivero@example.com', 'phone' => '72000009', 'address' => 'Cobija', 'gender' => 'Masculino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000035', 'first_name' => 'Martin Aguilar Mendez', 'birth_date' => '2012-09-18', 'email' => 'martin.aguilar@example.com', 'phone' => '72000010', 'address' => 'Santa Cruz', 'gender' => 'Masculino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000036', 'first_name' => 'Diego Suarez Ortiz', 'birth_date' => '2011-04-23', 'email' => 'diego.suarez@example.com', 'phone' => '72000011', 'address' => 'La Paz', 'gender' => 'Masculino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000037', 'first_name' => 'Tomas Pereira Luna', 'birth_date' => '2010-10-12', 'email' => 'tomas.pereira@example.com', 'phone' => '72000012', 'address' => 'Cochabamba', 'gender' => 'Masculino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000038', 'first_name' => 'Samuel Paredes Arce', 'birth_date' => '2009-03-01', 'email' => 'samuel.paredes@example.com', 'phone' => '72000013', 'address' => 'Tarija', 'gender' => 'Masculino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000039', 'first_name' => 'Leonardo Torres Cabrera', 'birth_date' => '2008-07-21', 'email' => 'leonardo.torres@example.com', 'phone' => '72000014', 'address' => 'Sucre', 'gender' => 'Masculino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000040', 'first_name' => 'Adrian Gutierrez Duran', 'birth_date' => '2007-02-05', 'email' => 'adrian.gutierrez@example.com', 'phone' => '72000015', 'address' => 'Oruro', 'gender' => 'Masculino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000041', 'first_name' => 'Daniel Navarro Salinas', 'birth_date' => '2006-12-27', 'email' => 'daniel.navarro@example.com', 'phone' => '72000016', 'address' => 'Potosi', 'gender' => 'Masculino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000042', 'first_name' => 'Sebastian Arias Roca', 'birth_date' => '2005-06-10', 'email' => 'sebastian.arias@example.com', 'phone' => '72000017', 'address' => 'Trinidad', 'gender' => 'Masculino', 'sangre' => 'O Rh (+)'],
            ['ci' => '90000043', 'first_name' => 'Rodrigo Campos Pinto', 'birth_date' => '2004-01-24', 'email' => 'rodrigo.campos@example.com', 'phone' => '72000018', 'address' => 'Cobija', 'gender' => 'Masculino', 'sangre' => 'A Rh (+)'],
            ['ci' => '90000044', 'first_name' => 'Cristian Molina Quiroga', 'birth_date' => '2003-09-03', 'email' => 'cristian.molina@example.com', 'phone' => '72000019', 'address' => 'Santa Cruz', 'gender' => 'Masculino', 'sangre' => 'B Rh (+)'],
            ['ci' => '90000045', 'first_name' => 'Pablo Cespedes Beltran', 'birth_date' => '2002-04-12', 'email' => 'pablo.cespedes@example.com', 'phone' => '72000020', 'address' => 'La Paz', 'gender' => 'Masculino', 'sangre' => 'AB Rh (+)'],
            ['ci' => '90000046', 'first_name' => 'Andres Ibarra Lopez', 'birth_date' => '2001-11-30', 'email' => 'andres.ibarra@example.com', 'phone' => '72000021', 'address' => 'Cochabamba', 'gender' => 'Masculino', 'sangre' => 'O Rh (-)'],
            ['ci' => '90000047', 'first_name' => 'Mauricio Delgadillo Mora', 'birth_date' => '2000-08-21', 'email' => 'mauricio.delgadillo@example.com', 'phone' => '72000022', 'address' => 'Tarija', 'gender' => 'Masculino', 'sangre' => 'A Rh (-)'],
            ['ci' => '90000048', 'first_name' => 'Esteban Villca Mercado', 'birth_date' => '1999-05-14', 'email' => 'esteban.villca@example.com', 'phone' => '72000023', 'address' => 'Sucre', 'gender' => 'Masculino', 'sangre' => 'B Rh (-)'],
            ['ci' => '90000049', 'first_name' => 'Javier Zambrana Nunez', 'birth_date' => '1998-10-05', 'email' => 'javier.zambrana@example.com', 'phone' => '72000024', 'address' => 'Oruro', 'gender' => 'Masculino', 'sangre' => 'AB Rh (-)'],
            ['ci' => '90000050', 'first_name' => 'Fernando Ortega Molina', 'birth_date' => '1996-06-19', 'email' => 'fernando.ortega@example.com', 'phone' => '72000025', 'address' => 'Potosi', 'gender' => 'Masculino', 'sangre' => 'O Rh (+)'],
        ];

        foreach ($personas as $persona) {
            // Sobrescribimos la fecha de nacimiento para que tengan entre 6 y 11 años aleatoriamente
            $persona['birth_date'] = fake()->dateTimeBetween('-11 years', '-6 years')->format('Y-m-d');

            Persona::updateOrCreate(
                ['ci' => $persona['ci']],
                $persona + [
                    'image' => null,
                    'status' => 1,
                ]
            );
        }
    }
}
