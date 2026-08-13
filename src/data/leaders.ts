export interface Leader {
  id: string;
  category: 'row1' | 'row2' | 'row3';
  badge: string;
  name: string;
  designation: string;
  role: string;
  photo: string;
  bio: string;
  fullStatement: string;
  achievements: string[];
}

export const esteemedLeaders: Leader[] = [
  // ROW 1
  {
    id: 'secretary_nishant_varma',
    category: 'row1',
    badge: 'Secretary cum Correspondent',
    name: 'Sri. S. R. K. Nishant Varma',
    designation: 'Secretary cum Correspondent',
    role: 'S.R.K.R. Engineering College Association',
    photo: 'assets/images/leaders/secretary_nishant_varma.jpg',
    bio: 'Fostering institutional development, administrative excellence, and innovative student welfare programs at SRKREC.',
    fullStatement: 'Our commitment is to provide students with state-of-the-art infrastructure, world-class technical education, and vibrant opportunities to excel in global careers.',
    achievements: [
      'Executive Leadership of SRKR Engineering College Association',
      'Expanded Modern Campus Infrastructure & Tech Suites',
      'Promoted Industry Collaboration & Student Welfare Funds'
    ]
  },
  {
    id: 'director_jagapathi_raju',
    category: 'row1',
    badge: 'Director',
    name: 'DR. M. Jagapathi Raju',
    designation: 'Director',
    role: 'S.R.K.R. Engineering College',
    photo: 'assets/images/leaders/director_jagapathi_raju.jpg',
    bio: 'Steering strategic institutional expansion, high-impact research endeavors, and global academic affiliations at SRKR Engineering College.',
    fullStatement: 'As Director of SRKR Engineering College, my vision is to continuously elevate our academic standards, foster world-class research facilities, and establish global industry collaborations.',
    achievements: [
      'Over 35 Years of Academic & Administrative Leadership',
      'Spearheaded Major Research & Infrastructure Initiatives',
      'Fostered Global University & Corporate Partnerships'
    ]
  },

  // ROW 2
  {
    id: 'principal_murali_krishnam_raju',
    category: 'row2',
    badge: 'Principal',
    name: 'Dr. K. V. Murali Krishnam Raju',
    designation: 'Principal',
    role: 'S.R.K.R. Engineering College',
    photo: 'assets/images/leaders/principal_murali_krishnam_raju.png',
    bio: 'Championing academic excellence, outcome-based education, accreditation quality, and holistic student development.',
    fullStatement: 'Education is the foundation for personal growth and societal transformation. At SRKR Engineering College, we are dedicated to cultivating an environment of intellectual curiosity and technical learning.',
    achievements: [
      'Driving NAAC \'A+\' Grade Quality & Accreditation Standards',
      'Published Numerous International Research Papers',
      'Mentored Generations of Successful Engineering Graduates'
    ]
  },
  {
    id: 'gb_vijaya_narasimha_raju',
    category: 'row2',
    badge: 'Member, Governing Body',
    name: 'Dr. K. S. Vijaya Narasimha Raju',
    designation: 'Member, Governing Body',
    role: 'S.R.K.R. Engineering College Governing Body',
    photo: 'assets/images/leaders/gb_vijaya_narasimha_raju.jpg',
    bio: 'Guiding institutional policy, academic governance, quality assurance, and long-term strategic initiatives.',
    fullStatement: 'Strong governance ensures academic rigor and continuous institutional innovation. As a member of the Governing Body, I am committed to supporting policies that promote research excellence.',
    achievements: [
      'Governing Body Representative for Academic Quality Assurance',
      'Guided Curriculum Innovations & Institutional Policies',
      'Promoted Interdisciplinary Research & Faculty Development'
    ]
  },

  // ROW 3
  {
    id: 'gb_satya_pratik_varma',
    category: 'row3',
    badge: 'Member, Governing Body',
    name: 'Sri. S. Satya Pratik Varma',
    designation: 'Member, Governing Body',
    role: 'S.R.K.R. Engineering College Governing Body',
    photo: 'assets/images/leaders/gb_satya_pratik_varma.jpg',
    bio: 'Promoting technological innovation, student entrepreneurship, and campus infrastructure development.',
    fullStatement: 'Empowering the next generation of engineers with modern facilities, startup incubation, and practical problem-solving skills is central to our vision.',
    achievements: [
      'Active Contributor to Governing Body Strategic Planning',
      'Supported Startup Incubation & Tech Club Initiatives',
      'Advocated Modern Campus Amenities & Learning Spaces'
    ]
  },
  {
    id: 'cao_dileep_chakravarthy',
    category: 'row3',
    badge: 'Chief Administrative Officer',
    name: 'Mr. Ch. Dileep Chakravarthy',
    designation: 'Chief Administrative Officer',
    role: 'S.R.K.R. Engineering College Administration',
    photo: 'assets/images/leaders/cao_dileep_chakravarthy.png',
    bio: 'Managing administrative operations, student infrastructure, campus governance, and institutional efficiency.',
    fullStatement: 'Efficient administrative governance is vital for maintaining a modern, seamless learning environment. We ensure state-of-the-art campus infrastructure and robust support services.',
    achievements: [
      'Overseeing 30+ Acre Campus Infrastructure & Modern Amenities',
      'Streamlined Student Support & Administrative Operations',
      'Fostered Campus Security, Wellness & Student Activity Facilities'
    ]
  }
];
